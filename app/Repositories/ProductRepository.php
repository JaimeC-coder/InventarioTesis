<?php

namespace App\Repositories;

use App\Enum\PurchasesStatusEnum;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Record;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

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

    public const OPEN_PURCHASE_MAX_AGE_DAYS = 10;

    /**
     * Excluye (vía whereNotExists) los productos que ya tienen una compra ABIERTA
     * (PENDIENTE/REGISTRADO/PEDIDO) reciente para ese mismo almacén — "reciente" =
     * con fecha dentro de los últimos OPEN_PURCHASE_MAX_AGE_DAYS días. RECIBIDO y
     * ANULADO son estados terminales y nunca bloquean. Una compra abierta más
     * vieja que ese umbral se considera estancada/abandonada y deja de bloquear,
     * para no ocultar el producto para siempre.
     *
     * $query debe tener ya un join a `products` y `records` en el FROM (usa
     * records.product_id / records.warehouse_id para correlacionar), sea un
     * query builder plano o un builder de Eloquent.
     */
    public function excludeProductsWithOpenRecentPurchase($query): void
    {
        $query->whereNotExists(function ($sub): void {
            $sub->selectRaw(1)
                ->from('productables')
                ->join('purchases', function ($join): void {
                    $join->on('purchases.id', '=', 'productables.productable_id')
                        ->where('productables.productable_type', Purchase::class);
                })
                ->whereColumn('productables.product_id', 'records.product_id')
                ->whereColumn('purchases.warehouse_id', 'records.warehouse_id')
                ->whereIn('purchases.status', [
                    PurchasesStatusEnum::PENDIENTE->value,
                    PurchasesStatusEnum::REGISTRADO->value,
                    PurchasesStatusEnum::PEDIDO->value,
                ])
                ->where('purchases.date', '>=', now()->subDays(self::OPEN_PURCHASE_MAX_AGE_DAYS)->toDateString())
                ->whereNull('purchases.deleted_at');
        });
    }

    /**
     * Productos con stock (por almacén) en o por debajo de su mínimo, excluyendo
     * los que ya tienen una compra abierta reciente (ver
     * excludeProductsWithOpenRecentPurchase). Filtra y limita en la base de
     * datos en vez de traer toda la tabla records a memoria.
     */
    public function groupedByWarehouseAndSupplier(int $limit = 30): Collection
    {
        $builder = Record::query()
            ->join('products', 'products.id', '=', 'records.product_id')
            ->where('products.is_active_product', 1)
            ->whereNull('products.deleted_at')
            ->whereColumn('records.quantity', '<=', 'products.min_stock');
        $this->excludeProductsWithOpenRecentPurchase($builder);
        $recordIds = $builder->orderBy('records.id')->limit($limit)->pluck('records.id');
        Log::info('Record IDs for low stock report: ', $recordIds->toArray());

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
