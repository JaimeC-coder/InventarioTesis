<?php

namespace App\Livewire\Admin\Tables;

use App\Models\Purchase;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PurchaseTable extends DataTableComponent
{
    protected $model = Purchase::class;

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
            Column::make('Serie', 'serie')
                ->sortable(),
            Column::make('Correlativo', 'correlativo')
                ->sortable(),
            Column::make('Purchase order id', 'purchaseOrder.serie')
                ->sortable(),
            Column::make('Date', 'date')
                ->sortable()
                ->format(fn($value): string => \Carbon\Carbon::parse($value)->format('d/m/Y')),
            Column::make('Supplier', 'supplier.name')
                ->sortable(),
            Column::make('Warehouse', 'warehouse.name')
                ->sortable(),
            Column::make('Total', 'total')
                ->sortable()
                ->format(fn($value): string => 'S/.' . number_format($value, 2)),
            Column::make('Uuid', 'uuid')
                ->sortable()->hideIf(true),
            Column::make('Created at', 'created_at')
                ->sortable()->format(fn($value): string => \Carbon\Carbon::parse($value)->format('d/m/Y H:i')),
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
        return Purchase::query()->with(['supplier','purchaseOrder','warehouse']);
    }
}
