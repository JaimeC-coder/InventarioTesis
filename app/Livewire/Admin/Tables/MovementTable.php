<?php

namespace App\Livewire\Admin\Tables;

use App\Models\Movement;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class MovementTable extends DataTableComponent
{
    protected $model = Movement::class;

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
                ->format(fn($value): string => match($value) {
                    '1' => 'Ingreso',
                    '2' => 'Salida',
                    default => 'Desconocido',
                })
            ,
            Column::make('Serie', 'serie')
                ->sortable(),
            Column::make('Correlativo', 'correlativo')
                ->sortable(),
            Column::make('Total', 'total')
                ->sortable(),
            Column::make('Warehouse', 'warehouse.name')
                ->sortable(),
            Column::make('Reason', 'reason.name')
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
        return Movement::query()->with(['warehouse', 'reason']);
    }
}
