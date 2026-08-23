<?php

use App\Http\Controllers\ProductController;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Quote;
use App\Models\Reason;
use App\Models\Record;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::post('suppliers', function (Request $request) {
    $supplier = Supplier::select('uuid', 'name')
        ->when($request->search, function ($query) use ($request): void {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('document_number', 'like', '%' . $request->search . '%');
        })
        ->when(
            $request->exists('selected'),
            fn($query) => $query->whereIn('uuid', $request->input('selected')),
            fn($query) => $query->limit(10)
        )
        ->get();
    return response()->json($supplier);
})->name('admin.suppliers');

//productos por proveedor
Route::post('products_suppliers', function (Request $request) {
    // $suplier_id = Supplier::where('uuid', $request->supplier_uuid)->value('id');
    Log::info('products_suppliers', ['supplier_uuid' => $request->supplier_uuid, 'request' => $request->all()]);
    $suplier_uuid = $request->input('supplier_uuid');
    Log::info('suplier_uuid', ['supplier_uuid' => $suplier_uuid, 'request' => $request->all()]);
    $search = $request->filled('search') && mb_strlen($request->search) >= 3
        ? $request->search
        : null;
    $cacheKey = sprintf(
        'products_wh_%s_%s',
        $suplier_uuid,
        md5(json_encode(['search' => $search, 'selected' => $request->selected]))
    );
    if ($cached = Cache::get($cacheKey)) {
        return response()->json($cached);
    }

    Log::info('products_suppliers cache miss', ['suplier_uuid' => $suplier_uuid]);
    // 2. Cache miss: ejecuta la query
    $query = Product::select('products.uuid', 'products.name')
        ->join('suppliers', 'suppliers.id', '=', 'products.supplier_id')
        ->where('suppliers.uuid', $suplier_uuid)
        ->whereNotNull('products.productBase_id');
    if ($search) {
        $query->where(function ($q) use ($search): void {
            $q->where('products.name', 'like', $search . '%')
                ->orWhere('products.barcode', 'like', $search . '%');
        });
    }

    if ($request->has('selected') && !empty($request->selected)) {
        $query->whereIn('products.uuid', $request->selected);
    } else {
        $query->limit(10);
    }

    Log::info('products_suppliers query', ['query' => $query->toSql(), 'bindings' => $query->getBindings()]);
    Log::info('products_suppliers cacheKey', ['cacheKey' => $cacheKey]);
    Log::info('products_suppliers search', ['search' => $search]);
    $results = $query->get();
    Log::info('products_suppliers results', ['results' => $results]);
    // 3. Solo guarda en caché si SÍ hay resultados
    if ($results->isNotEmpty()) {
        Cache::put($cacheKey, $results, 300); // 5 minutos
    }

    return response()->json($results);
})->name('admin.products_suppliers');

//productos por almacen
Route::post('products_warehouses', function (Request $request) {
    $warehouse_uuid = $request->warehouse_uuid ?? $request->input('warehouse_uuid');
    Log::info('products_warehouses', ['warehouse_uuid' => $warehouse_uuid, 'request' => $request->all()]);
    // Si el search es muy corto, lo tratamos como si no existiera
    $search = $request->filled('search') && mb_strlen($request->search) >= 3
        ? $request->search
        : null;
    $cacheKey = sprintf(
        'products_wh_%s_%s',
        $warehouse_uuid,
        md5(json_encode(['search' => $search, 'selected' => $request->selected]))
    );

    return Cache::remember($cacheKey, 300, function () use ($request, $warehouse_uuid, $search) {
        $builder = Record::query()
            ->join('products', 'products.id', '=', 'records.product_id')
            ->join('warehouses', 'warehouses.id', '=', 'records.warehouse_id')
            ->where('warehouses.uuid', $warehouse_uuid)
            ->whereNotNull('products.productBase_id')
            ->select('products.uuid', 'products.name', 'products.barcode');
        if ($search) {
            $builder->where(function ($q) use ($search): void {
                $q->where('products.name', 'like', $search . '%')
                    ->orWhere('products.barcode', 'like', $search . '%');
            });
        }

        if ($request->has('selected') && !empty($request->selected)) {
            $builder->whereIn('products.uuid', $request->selected);
        } else {
            $builder->limit(10);
        }

        return response()->json($builder->get(['uuid', 'name']));
    });
})->name('admin.products_warehouses');

//productos que el almacen no tiene

Route::post('warehouses', function (Request $request) {
    $cacheKey = 'warehouses_' . md5(json_encode($request->all()));
    return Cache::remember($cacheKey, 300, function () use ($request) { // 5 minutos
        $query = Warehouse::select('uuid', 'name');
        if ($search = $request->search) {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', $search . '%')
                    ->orWhere('sku', 'like', $search . '%');
            });
        }

        if ($request->has('selected') && !empty($request->selected)) {
            $query->whereIn('uuid', $request->selected);
        } else {
            $query->limit(10);
        }

        return response()->json($query->get());
    });
})->name('admin.warehouses');

Route::post('purchases-orders', function (Request $request) {
    $purchaseOrder = PurchaseOrder::when($request->search, function ($query) use ($request): void {
        $parts = explode('-', $request->search);
        if (count($parts) == 1) {
            $query->whereHas('supplier', function ($q) use ($request): void {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('document_number', 'like', '%' . $request->search . '%');
            });
            return;
        }

        if (count($parts) === 2) {
            $serie = $parts[0];
            $correlativo = ltrim($parts[1], '0');
            $query->where('serie', $serie)
                ->where('correlativo', 'like', '%' . $correlativo . '%');
            return;
        }
    })
        ->when(
            $request->exists('selected'),
            fn($query) => $query->whereIn('uuid', $request->input('selected')),
            fn($query) => $query->limit(10)
        )
        ->with(['supplier'])
        ->orderBy('created_at', 'desc')
        ->get();
    //str_pad($po->correlativo, 6, '0', STR_PAD_LEFT)

    return $purchaseOrder->map(function ($po): array {
        return [
            'uuid' => $po->uuid,
            'name' => $po->serie . ' - ' . $po->correlativo,
            'description' => $po->supplier->name . ' - ' . $po->supplier->document_number,
        ];
    });
    //return response()->json($purchaseOrder);
})->name('admin.purchases-orders');

Route::post('quotes', function (Request $request) {
    $quote = Quote::when($request->search, function ($query) use ($request): void {
        $parts = explode('-', $request->search);
        if (count($parts) == 1) {
            $query->whereHas('customer', function ($q) use ($request): void {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('document_number', 'like', '%' . $request->search . '%');
            });
            return;
        }

        if (count($parts) === 2) {
            $serie = $parts[0];
            $correlativo = ltrim($parts[1], '0');
            $query->where('serie', $serie)
                ->where('correlativo', 'like', '%' . $correlativo . '%');
            return;
        }
    })
        ->when(
            $request->exists('selected'),
            fn($query) => $query->whereIn('uuid', $request->input('selected')),
            fn($query) => $query->limit(10)
        )
        ->with(['customer'])
        ->orderBy('created_at', 'desc')
        ->get();
    //str_pad($po->correlativo, 6, '0', STR_PAD_LEFT)

    return $quote->map(function ($po): array {
        return [
            'uuid' => $po->uuid,
            'name' => $po->serie . ' - ' . $po->correlativo,
            'description' => $po->customer->name . ' - ' . $po->customer->document_number,
        ];
    });
    //return response()->json($purchaseOrder);
})->name('admin.quotes');

Route::post('customers', function (Request $request) {
    $cacheKey = 'customers_' . md5(json_encode($request->all()));
    return Cache::remember($cacheKey, 300, function () use ($request) { // 5 minutos
        $query = Customer::select('uuid', 'name', 'type')
            ->when($request->search, function ($query) use ($request): void {
                $query->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('document_number', 'like', '%' . $request->search . '%');
            });
        if ($request->has('selected') && !empty($request->selected)) {
            $query->whereIn('uuid', $request->selected);
        } else {
            $query->limit(10);
        }

        return response()->json($query->get());
    });
})->name('admin.customers');

Route::post('reasons', function (Request $request) {
    $cacheKey = 'reasons_' . md5(json_encode($request->all()));
    return Cache::remember($cacheKey, 300, function () use ($request) { // 5 minutos
        $query = Reason::select('uuid', 'name')
            ->when($request->search, function ($query) use ($request): void {
                $query->where('name', 'like', '%' . $request->search . '%');
            })->where('type', $request->input('type', '')); // 1 ingreso, 2 salida
        if ($request->has('selected') && !empty($request->selected)) {
            $query->whereIn('uuid', $request->selected);
        } else {
            $query->limit(10);
        }

        return response()->json($query->get());
    });
})->name('admin.reasons');

//Productos Create
Route::post('categories', function (Request $request) {
    $category = Category::select('uuid', 'name')
        ->when($request->search, function ($query) use ($request): void {
            $query->where('name', 'like', '%' . $request->search . '%');
        })
        ->when(
            $request->exists('selected'),
            fn($query) => $query->whereIn('uuid', $request->input('selected')),
            fn($query) => $query->limit(15)
        )
        ->get();
    return response()->json($category);
})->name('admin.categories');

Route::post('units', function (Request $request) {
    $units = \App\Models\Unit::select('uuid', 'name')
        ->when($request->search, function ($query) use ($request): void {
            $query->where('name', 'like', '%' . $request->search . '%');
        })
        ->when(
            $request->exists('selected'),
            fn($query) => $query->whereIn('uuid', $request->input('selected')),
            fn($query) => $query->limit(10)
        )
        ->get();
    return response()->json($units);
})->name('admin.units');

Route::post('measures', function (Request $request) {
    $brands = \App\Models\Measure::select('uuid', 'name', 'description_for_product', 'code', 'abbreviation')
        ->when($request->search, function ($query) use ($request): void {
            $query->where('name', 'like', '%' . $request->search . '%');
        })
        ->when(
            $request->exists('selected'),
            fn($query) => $query->whereIn('uuid', $request->input('selected')),
            fn($query) => $query->limit(10)
        )
        ->get();
    return response()->json($brands);
})->name('admin.measures');

Route::post('baseProducts', function (Request $request) {
    $brands = \App\Models\Product::select('uuid', 'name')
        ->whereNull('productBase_id')
        ->when($request->search, function ($query) use ($request): void {
            $query->where('name', 'like', '%' . $request->search . '%');
        })
        ->when(
            $request->exists('selected'),
            fn($query) => $query->whereIn('uuid', $request->input('selected')),
            fn($query) => $query->limit(10)
        )
        ->get();
    return response()->json($brands);
})->name('admin.baseProducts');

Route::POST('masive-products', [ProductController::class, 'massiveProducts'])->name('admin.massive-products');
