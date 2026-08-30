<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\Record;

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
            ->selectRaw('SUM(records.quantity_total) as stock_level')
            ->join('products', 'products.id', '=', 'records.product_id')
            ->whereNull('records.deleted_at')
            ->groupBy('products.id', 'products.uuid', 'products.name', 'records.warehouse_name')
            ->orderBy('stock_level', 'asc')
            ->limit($limit)
            ->get();
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
