<?php

namespace App\Repositories;

use App\Models\Sale;

class SaleRepository
{
    public function avgTicket(array $filters): array
    {
        $builder = Sale::query()->whereNull('deleted_at');
        $this->applyDateFilters($builder, $filters, 'date');

        return ['avg_ticket' => (float) $builder->avg('total') ?? 0];
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
