<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchReasonRequest;
use App\Http\Requests\SearchSelectRequest;
use App\Traits\HandlesSearchableSelect;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;

class GetApiController extends Controller
{
    use HandlesSearchableSelect;

    public function products(SearchSelectRequest $searchSelectRequest)
    {
        $products = \App\Models\Product::select('uuid', 'name')->whereNotNull('product_base_id');
        $result = $this->searchableSelect(
            cachePrefix: 'products',
            validated: $searchSelectRequest->validated(),
            searchCallback: function ($q, $search): void {
                $q->where(function ($sub) use ($search): void {
                    $sub->where('name', 'like', $search . '%')
                        ->orWhere('barcode', 'like', $search . '%');
                });
            },
            builder: $products
        );

        return response()->json($result);
    }

    public function suppliers(SearchSelectRequest $searchSelectRequest)
    {
        $supplier = \App\Models\Supplier::select('uuid', 'name');
        $result = $this->searchableSelect(
            cachePrefix: 'suppliers',
            validated: $searchSelectRequest->validated(),
            searchCallback: function ($q, $search): void {
                $q->where(function ($sub) use ($search): void {
                    $sub->where('name', 'like', $search . '%')
                        ->orWhere('document_number', 'like', '%' . $search . '%');
                });
            },
            builder: $supplier
        );

        return response()->json($result);
    }

    //?revisar
    public function productsSuppliers(SearchSelectRequest $searchSelectRequest)
    {
        $suplier_uuid = $searchSelectRequest->input('supplier_uuid');
        $search = $searchSelectRequest->filled('search') && mb_strlen($searchSelectRequest->search) >= 3
            ? $searchSelectRequest->search
            : null;
        $cacheKey = sprintf(
            'products_wh_%s_%s',
            $suplier_uuid,
            md5(json_encode(['search' => $search, 'selected' => $searchSelectRequest->selected]))
        );
        if ($cached = Cache::get($cacheKey)) {
            return response()->json($cached);
        }

        // 2. Cache miss: ejecuta la builder
        $builder = \App\Models\Product::select('products.uuid', 'products.name')
            ->join('suppliers', 'suppliers.id', '=', 'products.supplier_id')
            ->where('suppliers.uuid', $suplier_uuid)
            ->whereNotNull('products.product_base_id');
        if ($search) {
            $builder->where(function ($q) use ($search): void {
                $q->where('products.name', 'like', $search . '%')
                    ->orWhere('products.barcode', 'like', $search . '%');
            });
        }

        if ($searchSelectRequest->has('selected') && !empty($searchSelectRequest->selected)) {
            $builder->whereIn('products.uuid', $searchSelectRequest->selected);
        } else {
            $builder->limit(10);
        }

        $results = $builder->get();
        // 3. Solo guarda en caché si SÍ hay resultados
        if ($results->isNotEmpty()) {
            Cache::put($cacheKey, $results, 300); // 5 minutos
        }

        return response()->json($results);
    }

    //?revisar
    public function productsWarehouses(SearchSelectRequest $searchSelectRequest)
    {
        $warehouse_uuid = $searchSelectRequest->warehouse_uuid ?? $searchSelectRequest->input('warehouse_uuid');
        // Si el search es muy corto, lo tratamos como si no existiera
        $search = $searchSelectRequest->filled('search') && mb_strlen($searchSelectRequest->search) >= 3
            ? $searchSelectRequest->search
            : null;
        $cacheKey = sprintf(
            'products_wh_%s_%s',
            $warehouse_uuid,
            md5(json_encode(['search' => $search, 'selected' => $searchSelectRequest->selected]))
        );

        return Cache::remember($cacheKey, 300, function () use ($searchSelectRequest, $warehouse_uuid, $search) {
            $builder = \App\Models\Record::builder()
                ->join('products', 'products.id', '=', 'records.product_id')
                ->join('warehouses', 'warehouses.id', '=', 'records.warehouse_id')
                ->where('warehouses.uuid', $warehouse_uuid)
                ->whereNotNull('products.product_base_id')
                ->select('products.uuid', 'products.name', 'products.barcode');
            if ($search) {
                $builder->where(function ($q) use ($search): void {
                    $q->where('products.name', 'like', $search . '%')
                        ->orWhere('products.barcode', 'like', $search . '%');
                });
            }

            if ($searchSelectRequest->has('selected') && !empty($searchSelectRequest->selected)) {
                $builder->whereIn('products.uuid', $searchSelectRequest->selected);
            } else {
                $builder->limit(10);
            }

            return response()->json($builder->get(['uuid', 'name']));
        });
    }

    public function warehouses(SearchSelectRequest $searchSelectRequest)
    {
        $warehouses = \App\Models\Warehouse::select('uuid', 'name');
        $result = $this->searchableSelect(
            cachePrefix: 'warehouses',
            validated: $searchSelectRequest->validated(),
            searchCallback: function ($q, $search): void {
                $q->where(function ($sub) use ($search): void {
                    $sub->where('name', 'like', $search . '%')
                        ->orWhere('sku', 'like', '%' . $search . '%');
                });
            },
            builder: $warehouses
        );

        return response()->json($result);
    }

    public function purchasesOrders(SearchSelectRequest $searchSelectRequest)
    {
        $purchaseOrder = \App\Models\PurchaseOrder::when($searchSelectRequest->search, function ($builder) use ($searchSelectRequest): void {
            $parts = explode('-', $searchSelectRequest->search);
            if (count($parts) == 1) {
                $builder->whereHas('supplier', function ($q) use ($searchSelectRequest): void {
                    $q->where('name', 'like', '%' . $searchSelectRequest->search . '%')
                        ->orWhere('document_number', 'like', '%' . $searchSelectRequest->search . '%');
                });
                return;
            }

            if (count($parts) === 2) {
                $serie = $parts[0];
                $correlativo = ltrim($parts[1], '0');
                $builder->where('serie', $serie)
                    ->where('correlativo', 'like', '%' . $correlativo . '%');
                return;
            }
        })
            ->when(
                $searchSelectRequest->exists('selected'),
                fn($builder) => $builder->whereIn('uuid', $searchSelectRequest->input('selected')),
                fn($builder) => $builder->limit(10)
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

    public function quotes(SearchSelectRequest $searchSelectRequest)
    {
        $quote = \App\Models\Quote::when($searchSelectRequest->search, function ($builder) use ($searchSelectRequest): void {
            $parts = explode('-', $searchSelectRequest->search);
            if (count($parts) == 1) {
                $builder->whereHas('customer', function ($q) use ($searchSelectRequest): void {
                    $q->where('name', 'like', '%' . $searchSelectRequest->search . '%')
                        ->orWhere('document_number', 'like', '%' . $searchSelectRequest->search . '%');
                });
                return;
            }

            if (count($parts) === 2) {
                $serie = $parts[0];
                $correlativo = ltrim($parts[1], '0');
                $builder->where('serie', $serie)
                    ->where('correlativo', 'like', '%' . $correlativo . '%');
                return;
            }
        })
            ->when(
                $searchSelectRequest->exists('selected'),
                fn($builder) => $builder->whereIn('uuid', $searchSelectRequest->input('selected')),
                fn($builder) => $builder->limit(10)
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

    public function customers(SearchSelectRequest $searchSelectRequest)
    {
        $customers = \App\Models\Customer::select('uuid', 'name', 'type');
        $result = $this->searchableSelect(
            cachePrefix: 'customers',
            validated: $searchSelectRequest->validated(),
            searchCallback: function ($q, $search): void {
                $q->where(function ($sub) use ($search): void {
                    $sub->where('name', 'like', $search . '%')
                        ->orWhere('document_number', 'like', '%' . $search . '%');
                });
            },
            builder: $customers
        );

        return response()->json($result);
    }

    public function roles(SearchSelectRequest $searchSelectRequest)
    {
        $roles = Role::select('id', 'name');
        $result = $this->searchableSelect(
            cachePrefix: 'roles',
            validated: $searchSelectRequest->validated(),
            searchCallback: function ($q, $search): void {
                $q->where(function ($sub) use ($search): void {
                    $sub->where('name', 'like', $search . '%');
                });
            },
            builder: $roles
        );

        return response()->json($result);
    }

    public function reasons(SearchReasonRequest $searchReasonRequest)
    {
        $cacheKey = 'reasons_' . md5(json_encode($searchReasonRequest->all()));
        return Cache::remember($cacheKey, 300, function () use ($searchReasonRequest) { // 5 minutos
            $builder = \App\Models\Reason::select('uuid', 'name')
                ->when($searchReasonRequest->search, function ($builder) use ($searchReasonRequest): void {
                    $builder->where('name', 'like', '%' . $searchReasonRequest->search . '%');
                })->where('type', $searchReasonRequest->input('type', '')); // 1 ingreso, 2 salida
            if ($searchReasonRequest->has('selected') && !empty($searchReasonRequest->selected)) {
                $builder->whereIn('uuid', $searchReasonRequest->selected);
            } else {
                $builder->limit(10);
            }

            return response()->json($builder->get());
        });
    }

    public function categories(SearchSelectRequest $searchSelectRequest)
    {
        $categories = \App\Models\Category::select('uuid', 'name');
        $result = $this->searchableSelect(
            cachePrefix: 'categories',
            validated: $searchSelectRequest->validated(),
            searchCallback: function ($q, $search): void {
                $q->where(function ($sub) use ($search): void {
                    $sub->where('name', 'like', '%' . $search . '%');
                });
            },
            builder: $categories,
            limit: 15,
        );

        return response()->json($result);
    }

    public function units(SearchSelectRequest $searchSelectRequest)
    {
        $units = \App\Models\Unit::select('uuid', 'name');
        $result = $this->searchableSelect(
            cachePrefix: 'units',
            validated: $searchSelectRequest->validated(),
            searchCallback: function ($q, $search): void {
                $q->where(function ($sub) use ($search): void {
                    $sub->where('name', 'like', '%' . $search . '%');
                });
            },
            builder: $units,
            limit: 10,
        );

        return response()->json($result);
    }

    public function measures(SearchSelectRequest $searchSelectRequest)
    {
        $measures = \App\Models\Measure::select('uuid', 'name', 'description_for_product', 'code', 'abbreviation');
        $result = $this->searchableSelect(
            cachePrefix: 'measures',
            validated: $searchSelectRequest->validated(),
            searchCallback: function ($q, $search): void {
                $q->where(function ($sub) use ($search): void {
                    $sub->where('name', 'like', '%' . $search . '%');
                });
            },
            builder: $measures,
            limit: 15,
        );

        return response()->json($result);
    }

    public function baseProducts(SearchSelectRequest $searchSelectRequest)
    {
        $productsBase = \App\Models\Product::select('uuid', 'name')->whereNull('product_base_id');
        $result = $this->searchableSelect(
            cachePrefix: 'productsBase',
            validated: $searchSelectRequest->validated(),
            searchCallback: function ($q, $search): void {
                $q->where(function ($sub) use ($search): void {
                    $sub->where('name', 'like', '%' . $search . '%');
                });
            },
            builder: $productsBase
        );

        return response()->json($result);
    }
}
