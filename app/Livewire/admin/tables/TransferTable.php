<?php

namespace App\Livewire\admin\tables;

use App\Models\Transfer;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class TransferTable extends DataTableComponent
{
    protected $model = Transfer::class;

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
            Column::make('Type', 'type')
                ->sortable()
                ->format(fn($value): string => match ($value) {
                    '1' => 'Ingreso',
                    '2' => 'Salida',
                    default => 'Desconocido',
                }),
            Column::make('Serie', 'serie')
                ->sortable(),
            Column::make('Correlativo', 'correlativo')
                ->sortable(),
            Column::make('Date', 'date')
                ->sortable(),
            Column::make('Total', 'total')
                ->sortable(),
            Column::make('Observaciones', 'observaciones')
                ->sortable(),
            Column::make('Origin warehouse', 'originWarehouse.name')
                ->sortable(),
            Column::make('Destination warehouse', 'destinationWarehouse.name')
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
        return Transfer::query()->with(['originWarehouse', 'destinationWarehouse']);
    }
}
