<?php

namespace App\Livewire\Admin\Tables;

use App\Models\Movement;
use App\Services\FileServices;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
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
        return Movement::query()->with(['warehouse', 'reason']);
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
            ->add('observation')
            ->add('total')
            ->add('warehouse.name')
            ->add('reason.name')
            ->add('uuid')
            ->add('created_at')->add('created_at_formatted', fn($user): string => Carbon::parse($user->created_at)->format('d/m/Y H:i:s'));;
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
            Column::make('observation', 'observation')
                ->sortable()
                ->searchable(),
            Column::make('Total', 'total')
                ->sortable()
                ->searchable(),
            Column::make('Warehouse id', 'warehouse.name')
                ->sortable()
                ->searchable(),
            Column::make('Reason id', 'reason.name')
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
        return [];
    }

    #[\Livewire\Attributes\On('pdf')]
    public function pdf(string $rowId): void
    {
        $movement = Movement::whereUuid($rowId)->first();
        Log::info('GENERANDO PDF EN TABLA DE MOVIMIENTOS PARA: ', [$movement]);
        if (is_null($movement->file_path) || $movement->file_path === '') {
            $model = Movement::class;
            $payload = [
                'model' => $model,
                'uuids' => $rowId,
            ];
            $file = FileServices::generatePdfNow($payload);
            Log::info('PDF GENERADO EN TABLA DE MOVIMIENTOS: ' . $file);
            $routeFile = $file;
            $movement->file_path = $routeFile;
            $movement->save();
        } else {
            $routeFile = $movement->file_path;
        }

        $routeFile = FileServices::url($routeFile);
        $this->js("
            const pdfUrl = '{$routeFile}';
            window.open(pdfUrl, '_blank');
        ");
    }

    public function actions(Movement $movement): array
    {
        return [
            Button::add('pdf')
                ->slot('PDF: ' . $movement->serie)
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('pdf', ['rowId' => $movement->uuid]),
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
