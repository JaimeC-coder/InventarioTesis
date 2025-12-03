<?php

namespace App\Livewire\Admin\Tables;

use App\Models\Quote;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class QuoteTable extends PowerGridComponent
{
    public string $tableName = 'quote-table-uaccbg-table';

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
        return Quote::query()->with(['customer']);
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
            ->add('date_formatted', fn(Quote $quote): string => Carbon::parse($quote->date)->format('d/m/Y'))
            ->add('total')
            ->add('observation')
            ->add('customer.name')
            ->add('uuid')
            ->add('created_at') ->add('created_at_formatted', fn($user): string => Carbon::parse($user->created_at)->format('d/m/Y H:i:s'));
        ;
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
            Column::make('Total', 'total')
                ->sortable()
                ->searchable(),
            Column::make('Observation', 'observation')
                ->sortable()
                ->searchable(),
            Column::make('Customer id', 'customer.name')
                ->sortable()
                ->searchable(),
            Column::make('Uuid', 'uuid')
                ->sortable()
                ->searchable(),
            Column::make('Creado el', 'created_at_formatted', 'created_at')
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

    public function actions(Quote $quote): array
    {
        return [
            Button::add('edit')
                ->slot('Edit: '.$quote->id)
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit', ['rowId' => $quote->id]),
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
