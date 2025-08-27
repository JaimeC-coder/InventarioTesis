<?php

namespace App\Livewire\admin;

use App\Models\Quote;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class QuoteTable extends DataTableComponent
{
    protected $model = Quote::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make('Voucher type', 'voucher_type')
                ->sortable(),
            Column::make('Serie', 'serie')
                ->sortable(),
            Column::make('Correlativo', 'correlativo')
                ->sortable(),
            Column::make('Date', 'date')
                ->sortable(),
            Column::make('Total', 'total')
                ->sortable(),
            Column::make('Observation', 'observation')
                ->sortable(),
            Column::make('Customer id', 'customer_id')
                ->sortable(),
            Column::make('Uuid', 'uuid')
                ->sortable(),
            Column::make('Created at', 'created_at')
                ->sortable(),
        ];
    }
}
