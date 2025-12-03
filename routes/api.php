<?php

use App\Http\Controllers\ProductController;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Quote;
use App\Models\Reason;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;
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

Route::post('products', function (Request $request) {
    $product = Product::select('uuid', 'name')
        ->whereNotNull('productBase_id')
        ->when($request->search, function ($query) use ($request): void {
            $query->where(function ($q) use ($request): void {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('barcode', 'like', '%' . $request->search . '%');
            });
        })
        ->when(
            $request->exists('selected'),
            fn($query) => $query->whereIn('uuid', $request->input('selected')),
            fn($query) => $query->limit(10)
        )
        ->get();
    Log::info('Productos encontrados: ' . $product->count());
    return response()->json($product);
})->name('admin.products');

Route::post('warehouses', function (Request $request) {
    $warehouse = Warehouse::select('uuid', 'name')
        ->when($request->search, function ($query) use ($request): void {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('sku', 'like', '%' . $request->search . '%');
        })
        ->when(
            $request->exists('selected'),
            fn($query) => $query->whereIn('uuid', $request->input('selected')),
            fn($query) => $query->limit(10)
        )
        ->get();
    return response()->json($warehouse);
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
    $customer = Customer::select('customers.uuid', 'customers.name', 'customers.type')
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
    return response()->json($customer);
})->name('admin.customers');

Route::post('reasons', function (Request $request) {
    $reason = Reason::select('uuid', 'name')
        ->when($request->search, function ($query) use ($request): void {
            $query->where('name', 'like', '%' . $request->search . '%');
        })
        ->when(
            $request->exists('selected'),
            fn($query) => $query->whereIn('uuid', $request->input('selected')),
            fn($query) => $query->limit(10)
        )
        ->where('type', $request->input('type', '')) // 1 ingreso, 2 salida
        ->get();
    return response()->json($reason);
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
            fn($query) => $query->limit(10)
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
