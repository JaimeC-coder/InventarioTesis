<?php

namespace App\Livewire\Admin\Tables;

use App\Models\Movement;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class MovementTable extends PowerGridComponent
{
    public string $tableName = 'movement-table-10yutk-table';

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Movement::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('type')
            ->add('serie')
            ->add('correlativo')
            ->add('date')
            ->add('observaciones')
            ->add('total')
            ->add('warehouse_id')
            ->add('reason_id')
            ->add('uuid')
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Type', 'type')
                ->sortable()
                ->searchable(),
            Column::make('Serie', 'serie')
                ->sortable()
                ->searchable(),
            Column::make('Correlativo', 'correlativo')
                ->sortable()
                ->searchable(),
            Column::make('Date', 'date_formatted', 'date')
                ->sortable(),
            Column::make('Date', 'date')
                ->sortable()
                ->searchable(),
            Column::make('Observaciones', 'observaciones')
                ->sortable()
                ->searchable(),
            Column::make('Total', 'total')
                ->sortable()
                ->searchable(),
            Column::make('Warehouse id', 'warehouse_id')
                ->sortable()
                ->searchable(),
            Column::make('Reason id', 'reason_id')
                ->sortable()
                ->searchable(),
            Column::make('Uuid', 'uuid')
                ->sortable()
                ->searchable(),
            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable(),
            Column::make('Created at', 'created_at')
                ->sortable()
                ->searchable(),
            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit(string $rowId): void
    {
        $this->js('alert('.$rowId.')');
    }

    public function actions(Movement $movement): array
    {
        return [
            Button::add('edit')
                ->slot('Edit: '.$movement->id)
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit', ['rowId' => $movement->id]),
        ];
    }

    /*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
