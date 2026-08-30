<?php

namespace App\Repositories;

use App\Models\Purchase;
use App\Models\PurchaseOrder;
use App\Models\Quote;
use App\Models\Sale;

class ConversionRepository
{
    public function quoteToSaleRate(array $filters): array
    {
        $builder = Quote::query()->whereNull('deleted_at');
        $this->applyDateFilters($builder, $filters, 'date');
        $total = (clone $builder)->count();
        $converted = (clone $builder)->whereHas('sale')->count();

        return [
            'total_quotes' => $total,
            'converted_to_sale' => $converted,
            'conversion_rate' => $total > 0 ? round($converted / $total * 100, 2) : 0,
        ];
    }

    public function purchaseOrderFulfillment(array $filters): array
    {
        $builder = PurchaseOrder::query()->whereNull('deleted_at');
        $this->applyDateFilters($builder, $filters, 'date');
        $total = (clone $builder)->count();
        $fulfilled = (clone $builder)->whereHas('purchase')->count();

        return [
            'total_orders' => $total,
            'fulfilled' => $fulfilled,
            'fulfillment_rate' => $total > 0 ? round($fulfilled / $total * 100, 2) : 0,
        ];
    }

    public function purchasesVsSalesTotal(array $filters): array
    {
        $builder = Sale::query()->whereNull('deleted_at');
        $purchasesQuery = Purchase::query()->whereNull('deleted_at');
        $this->applyDateFilters($builder, $filters, 'date');
        $this->applyDateFilters($purchasesQuery, $filters, 'date');
        $totalSales = (float) (clone $builder)->sum('total');
        $totalPurchases = (float) (clone $purchasesQuery)->sum('total');

        return [
            'total_sales' => $totalSales,
            'total_purchases' => $totalPurchases,
            'difference' => $totalSales - $totalPurchases,
        ];
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
