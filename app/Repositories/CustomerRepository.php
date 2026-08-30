<?php

namespace App\Repositories;

use App\Models\Customer;

class CustomerRepository
{
    public function topByRevenue(array $filters, string $direction, int $limit)
    {
        $query = Customer::query()
            ->select('customers.uuid', 'customers.name')
            ->selectRaw('SUM(sales.total) as total_revenue')
            ->join('sales', 'sales.customer_id', '=', 'customers.id')
            ->whereNull('sales.deleted_at')
            ->groupBy('customers.id', 'customers.uuid', 'customers.name');

        $this->applyDateFilters($query, $filters, 'sales.date');

        return $query->orderBy('total_revenue', $direction)->limit($limit)->get();
    }

    public function topByPurchaseCount(array $filters, string $direction, int $limit)
    {
        $query = Customer::query()
            ->select('customers.uuid', 'customers.name')
            ->selectRaw('COUNT(sales.id) as purchase_count')
            ->join('sales', 'sales.customer_id', '=', 'customers.id')
            ->whereNull('sales.deleted_at')
            ->groupBy('customers.id', 'customers.uuid', 'customers.name');

        $this->applyDateFilters($query, $filters, 'sales.date');

        return $query->orderBy('purchase_count', $direction)->limit($limit)->get();
    }

    private function applyDateFilters($query, array $filters, string $column): void
    {
        if (!empty($filters['year']))      $query->whereYear($column, $filters['year']);
        if (!empty($filters['date_from'])) $query->where($column, '>=', $filters['date_from']);
        if (!empty($filters['date_to']))   $query->where($column, '<=', $filters['date_to']);
    }
}
