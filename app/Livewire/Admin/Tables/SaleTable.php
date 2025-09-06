<?php

namespace App\Livewire\Admin\Tables;

use App\Models\Sale;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class SaleTable extends PowerGridComponent
{
    public string $tableName = 'sale-table-xlbb5y-table';

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
        return Sale::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('voucher_type')
            ->add('serie')
            ->add('correlativo')
            ->add('date')
            ->add('quote_id')
            ->add('customer_id')
            ->add('warehouse_id')
            ->add('total')
            ->add('observation')
            ->add('uuid')
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Voucher type', 'voucher_type')
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

            Column::make('Quote id', 'quote_id')
                ->sortable()
                ->searchable(),

            Column::make('Customer id', 'customer_id')
                ->sortable()
                ->searchable(),

            Column::make('Warehouse id', 'warehouse_id')
                ->sortable()
                ->searchable(),

            Column::make('Total', 'total')
                ->sortable()
                ->searchable(),

            Column::make('Observation', 'observation')
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

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert('.$rowId.')');
    }

    public function actions(Sale $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit: '.$row->id)
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit', ['rowId' => $row->id])
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
