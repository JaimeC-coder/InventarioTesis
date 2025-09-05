<?php

namespace App\Livewire\Admin\Tables;

use App\Models\Sale;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class SaleTable extends DataTableComponent
{
    protected $model = Sale::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('created_at', 'desc');
    }

    public function columns(): array
    {
        return [
            Column::make('#')
                ->label(function ($row, Column $column): int {
                    return $this->getRowNumber();
                })
                ->sortable(),
            Column::make('Voucher type', 'voucher_type')
                ->sortable(),
            Column::make('Serie', 'serie')
                ->sortable(),
            Column::make('Correlativo', 'correlativo')
                ->sortable(),
            Column::make('Date', 'date')
                ->sortable(),
            Column::make('Quote', 'quote.id')
                ->sortable(),
            Column::make('Customer', 'customer.name')
                ->sortable(),
            Column::make('Warehouse', 'warehouse.name')
                ->sortable(),
            Column::make('Total', 'total')
                ->sortable(),
            Column::make('Observation', 'observation')
                ->sortable(),
            Column::make('Uuid', 'uuid')
                ->sortable()->sortable()->hideIf(true),
            Column::make('Fecha de creación', 'created_at')
                ->sortable(),
        ];
    }

    protected function getRowNumber(): int
    {
        static $position = null;
        if ($position === null) {
            $position = (($this->getPage() - 1) * $this->getPerPage()) + 1;
        } else {
            $position++;
        }

        return $position;
    }

    public function builder(): Builder
    {
        return Sale::query()->with(['customer', 'warehouse', 'quote']);
    }
}
