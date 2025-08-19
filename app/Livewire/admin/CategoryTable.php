<?php

namespace App\Livewire\admin;

use App\Models\Category;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class CategoryTable extends DataTableComponent
{
    protected $model = Category::class;

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
            Column::make('Nombre', 'name')
                ->sortable()
                ->searchable(),
            Column::make('Descripción', 'description')
                ->sortable()
                ->searchable(),
            Column::make('UUID', 'uuid')
                ->sortable()
                ->hideIf(true),
            Column::make('Fecha de creación', 'created_at')
                ->sortable()
                ->searchable(),
            Column::make('Acciones')
                ->label(function ($row, Column $column) {
                    // $row aquí es el modelo completo Category
                    return view('admin.categories.actions', [
                        'category' => $row, // Pasa el modelo completo
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
