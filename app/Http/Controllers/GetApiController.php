<?php

namespace App\Http\Controllers;

use App\Traits\HandlesSearchableSelect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GetApiController extends Controller
{
    use HandlesSearchableSelect;

    public function products(Request $request)
    {
        $products = \App\Models\Product::select('uuid', 'name')->whereNotNull('productBase_id');
        $result = $this->searchableSelect(
            cachePrefix: 'products',
            validated: $request->validated(),
            searchCallback: function ($q, $search): void {
                $q->where(function ($sub) use ($search): void {
                    $sub->where('name', 'like', $search . '%')
                        ->orWhere('barcode', 'like', $search . '%');
                });
            },
            query: $products
        );

        return response()->json($result);
    }

    public function suppliers(Request $request)
    {
        $supplier = \App\Models\Supplier::select('uuid', 'name');
        $result = $this->searchableSelect(
            cachePrefix: 'suppliers',
            validated: $request->validated(),
            searchCallback: function ($q, $search): void {
                $q->where(function ($sub) use ($search): void {
                    $sub->where('name', 'like', $search . '%')
                        ->orWhere('document_number', 'like', '%' . $search . '%');
                });
            },
            query: $supplier
        );

        return response()->json($result);
    }

    public function productsSuppliers(Request $request)
    {
        $suplier_uuid = $request->input('supplier_uuid');
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

        // 2. Cache miss: ejecuta la query
        $query = \App\Models\Product::select('products.uuid', 'products.name')
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

        $results = $query->get();
        // 3. Solo guarda en caché si SÍ hay resultados
        if ($results->isNotEmpty()) {
            Cache::put($cacheKey, $results, 300); // 5 minutos
        }

        return response()->json($results);
    }

    public function productsWarehouses(Request $request)
    {
        $warehouse_uuid = $request->warehouse_uuid ?? $request->input('warehouse_uuid');
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
            $builder = \App\Models\Record::query()
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
    }

    public function warehouses(Request $request)
    {
        $warehouses = \App\Models\Warehouse::select('uuid', 'name');
        $result = $this->searchableSelect(
            cachePrefix: 'warehouses',
            validated: $request->validated(),
            searchCallback: function ($q, $search): void {
                $q->where(function ($sub) use ($search): void {
                    $sub->where('name', 'like', $search . '%')
                        ->orWhere('sku', 'like', '%' . $search . '%');
                });
            },
            query: $warehouses
        );

        return response()->json($result);
    }

    public function purchasesOrders(Request $request)
    {
        $purchaseOrder = \App\Models\PurchaseOrder::when($request->search, function ($query) use ($request): void {
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
    }

    public function quotes(Request $request)
    {
        $quote = \App\Models\Quote::when($request->search, function ($query) use ($request): void {
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
    }

    public function customers(Request $request)
    {
        $customers = \App\Models\Customer::select('uuid', 'name', 'type');
        $result = $this->searchableSelect(
            cachePrefix: 'customers',
            validated: $request->validated(),
            searchCallback: function ($q, $search): void {
                $q->where(function ($sub) use ($search): void {
                    $sub->where('name', 'like', $search . '%')
                        ->orWhere('document_number', 'like', '%' . $search . '%');
                });
            },
            query: $customers
        );

        return response()->json($result);
    }

    public function reasons(Request $request)
    {
        $cacheKey = 'reasons_' . md5(json_encode($request->all()));
        return Cache::remember($cacheKey, 300, function () use ($request) { // 5 minutos
            $query = \App\Models\Reason::select('uuid', 'name')
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
    }

    public function categories(Request $request)
    {
        $categories = \App\Models\Category::select('uuid', 'name');
        $result = $this->searchableSelect(
            cachePrefix: 'categories',
            validated: $request->validated(),
            searchCallback: function ($q, $search): void {
                $q->where(function ($sub) use ($search): void {
                    $sub->where('name', 'like', '%' . $search . '%');
                });
            },
            query: $categories,
            limit: 15,
        );

        return response()->json($result);
    }

    public function units(Request $request)
    {
        $units = \App\Models\Unit::select('uuid', 'name');
        $result = $this->searchableSelect(
            cachePrefix: 'units',
            validated: $request->validated(),
            searchCallback: function ($q, $search): void {
                $q->where(function ($sub) use ($search): void {
                    $sub->where('name', 'like', '%' . $search . '%');
                });
            },
            query: $units,
            limit: 10,
        );

        return response()->json($result);
    }

    public function measures(Request $request)
    {
        $measures = \App\Models\Measure::select('uuid', 'name', 'description_for_product', 'code', 'abbreviation');
        $result = $this->searchableSelect(
            cachePrefix: 'measures',
            validated: $request->validated(),
            searchCallback: function ($q, $search): void {
                $q->where(function ($sub) use ($search): void {
                    $sub->where('name', 'like', '%' . $search . '%');
                });
            },
            query: $measures,
            limit: 15,
        );

        return response()->json($result);
    }

    public function baseProducts(Request $request)
    {
        $productsBase = \App\Models\Product::select('uuid', 'name')->whereNull('productBase_id');
        $result = $this->searchableSelect(
            cachePrefix: 'productsBase',
            validated: $request->validated(),
            searchCallback: function ($q, $search): void {
                $q->where(function ($sub) use ($search): void {
                    $sub->where('name', 'like', '%' . $search . '%');
                });
            },
            query: $productsBase
        );

        return response()->json($result);
    }
}
