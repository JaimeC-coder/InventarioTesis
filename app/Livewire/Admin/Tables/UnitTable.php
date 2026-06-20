<?php

namespace App\Livewire\Admin\Tables;

use App\Exports\GenericExport;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use Maatwebsite\Excel\Facades\Excel;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class UnitTable extends PowerGridComponent
{
    public string $primaryKey = 'uuid';

    public string $sortField = 'units.created_at';

    public string $tableName = 'unit-table-b5c7fx-table';

    public function setUp(): array
    {
        $this->showCheckBox('uuid');

        return [
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function header(): array
    {
        return [
            Button::add('pdf-export')
                ->slot('Exportar PDF (<span x-text="window.pgBulkActions.count(\'' . $this->tableName . '\')"></span>)')
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('exportPdf.' . $this->tableName, []),
            Button::add('excel-export')
                ->slot('Exportar Excel (<span x-text="window.pgBulkActions.count(\'' . $this->tableName . '\')"></span>)')
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('exportExcel.' . $this->tableName, []),
        ];
    }

    public function datasource(): Builder
    {
        return Unit::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('code')
            ->add('name')
            ->add('abbreviation')
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Nombre', 'name')
                ->sortable()
                ->searchable(),
            Column::make('Abreviatura', 'abbreviation')
                ->sortable()
                ->searchable(),
            Column::make('Codigo', 'code')
                ->sortable()
                ->searchable(),
            Column::make('Creado', 'created_at')
                ->sortable()
                ->searchable(),
        ];
    }

    public function filters(): array
    {
        return [];
    }

    //

    #[On('exportExcel.{tableName}')]
    public function exportExcel()
    {
        if ($this->checkboxValues === []) {
            $this->dispatch('swal', [
                'title' => 'Precaución',
                'text' => 'No se han seleccionado registros para exportar.',
                'icon' => 'warning',
            ]);
            return null;
        }

        $data = Unit::whereIn('uuid', $this->checkboxValues)->get();
        $headers = ['ID', 'Nombre', 'Abreviatura', 'Código'];

        return Excel::download(
            new GenericExport(
                data: $data,
                headers: $headers,
                mapping: function ($category): array {
                    return [
                        $category->id,
                        $category->name,
                        $category->abbreviation,
                        $category->code,
                    ];
                }
            ),
            'unidades-de-medida_export.xlsx'
        );
    }

    #[On('exportPdf.{tableName}')]
    public function exportPdf(): void
    {
        if ($this->checkboxValues === []) {
            $this->dispatch('swal', [
                'title' => 'Precaución',
                'text' => 'No se han seleccionado registros para exportar.',
                'icon' => 'warning',
            ]);
            return;
        }

        $uuids = $this->checkboxValues;
        $model = Unit::class;
        $headers = ['Nombre', 'Abreviatura', 'Código'];
        $titulo = 'Unidades de Medida';
        $columns = ['name', 'abbreviation', 'code'];
        $fileName = 'unidades-de-medida_export.pdf';
        // Enviar al componente PDF
        $this->dispatch('openPdfExport', $uuids, $model, $titulo, $columns, $headers, $fileName);
    }
}
