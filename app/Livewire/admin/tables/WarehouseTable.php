<?php

namespace App\Livewire\admin\tables;

use App\Models\Warehouse;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class WarehouseTable extends DataTableComponent
{
    protected $model = Warehouse::class;

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
            Column::make('Name', 'name')
                ->sortable(),
            Column::make('Location', 'location')
                ->sortable(),
            Column::make('UUID', 'uuid')
                ->sortable()
                ->hideIf(true),
            Column::make('Fecha de creación', 'created_at')
                ->sortable()
                ->searchable(),
            Column::make('Acciones')
                ->label(function ($row, Column $column) {
                    // $row aquí es el modelo completo Warehouse
                    return view('admin.warehouses.actions', [
                        'warehouse' => $row, // Pasa el modelo completo
                    ]);
                }),
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
}
