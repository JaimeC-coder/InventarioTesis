<?php

namespace App\Repositories;

use App\Enum\PurchasesStatusEnum;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Record;
use Illuminate\Support\Collection;

class ProductRepository
{
    public function topSold(array $filters, string $direction, int $limit)
    {
        $builder = Product::query()
            ->select('products.uuid', 'products.name')
            ->selectRaw('SUM(productables.quantity) as total_sold')
            ->join('productables', 'productables.product_id', '=', 'products.id')
            ->where('productables.productable_type', \App\Models\Sale::class) // clave: solo ventas
            ->whereNull('products.deleted_at')
            ->groupBy('products.id', 'products.uuid', 'products.name');
        $this->applyDateFilters($builder, $filters, 'productables.created_at');

        return $builder->orderBy('total_sold', $direction)->limit($limit)->get();
    }

    public function topPurchased(array $filters, string $direction, int $limit)
    {
        $builder = Product::query()
            ->select('products.uuid', 'products.name')
            ->selectRaw('SUM(productables.quantity) as total_purchased')
            ->join('productables', 'productables.product_id', '=', 'products.id')
            ->where('productables.productable_type', \App\Models\Purchase::class) // clave: solo compras
            ->whereNull('products.deleted_at')
            ->groupBy('products.id', 'products.uuid', 'products.name');
        $this->applyDateFilters($builder, $filters, 'productables.created_at');

        return $builder->orderBy('total_purchased', $direction)->limit($limit)->get();
    }

    public function stockReport(array $filters, int $limit)
    {
        // records es la fuente de verdad, nunca products.stock (que es derivado)
        return Record::query()
            ->select('products.uuid', 'products.name', 'records.warehouse_name')
            ->selectRaw('SUM(records.quantity) as stock_level')
            ->join('products', 'products.id', '=', 'records.product_id')
            ->whereNull('records.deleted_at')
            ->groupBy('products.id', 'products.uuid', 'products.name', 'records.warehouse_name')
            ->orderBy('stock_level', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Productos con stock (por almacén) en o por debajo de su mínimo, excluyendo
     * los que ya tienen una compra generada para ese mismo almacén (cualquier
     * estado salvo ANULADO cuenta como "ya se generó", incluido PENDIENTE, para
     * no duplicar el pedido). Filtra y limita en la base de datos en vez de traer
     * toda la tabla records a memoria.
     */
    public function groupedByWarehouseAndSupplier(int $limit = 30): Collection
    {
        $recordIds = Record::query()
            ->join('products', 'products.id', '=', 'records.product_id')
            ->where('products.is_active_product', 1)
            ->whereNull('products.deleted_at')
            ->whereColumn('records.quantity', '<=', 'products.min_stock')
            ->whereNotExists(function ($query): void {
                $query->selectRaw(1)
                    ->from('productables')
                    ->join('purchases', function ($join): void {
                        $join->on('purchases.id', '=', 'productables.productable_id')
                            ->where('productables.productable_type', Purchase::class);
                    })
                    ->whereColumn('productables.product_id', 'records.product_id')
                    ->whereColumn('purchases.warehouse_id', 'records.warehouse_id')
                    ->where('purchases.status', '!=', PurchasesStatusEnum::ANULADO->value)
                    ->whereNull('purchases.deleted_at');
            })
            ->orderBy('records.id')
            ->limit($limit)
            ->pluck('records.id');

        return Record::query()
            ->with(['product.supplier', 'warehouse'])
            ->whereIn('id', $recordIds)
            ->get()
            ->groupBy('warehouse_id')
            ->map(fn(Collection $byWarehouse) => $byWarehouse->groupBy('product.supplier_id'));
    }

    private function applyDateFilters($query, array $filters, string $column): void
    {
        if (!empty($filters['year'])) {
            $query->whereYear($column, $filters['year']);
        }

        if (!empty($filters['date_from'])) {
            $query->where($column, '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where($column, '<=', $filters['date_to']);
        }
    }
}
