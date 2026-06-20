<?php

namespace App\Livewire\Admin\Tables;

use App\Exports\GenericExport;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Maatwebsite\Excel\Facades\Excel;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class CategoryTable extends PowerGridComponent
{
    public string $primaryKey = 'uuid';

    public string $sortField = 'categories.created_at';

    public string $tableName = 'category-table-oc8dnv-table';

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
            Button::add('bulk-delete')
                ->slot('Eliminación masiva (<span x-text="window.pgBulkActions.count(\'' . $this->tableName . '\')"></span>)')
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('bulkDelete.' . $this->tableName, []),
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
        return Category::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('name')
            ->add('description', function ($dish): string {
                return '<div title="' . e($dish->description) . '" style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">' . e($dish->description) . '</div>';
            })
            ->add('uuid')
            ->add('codigo')
            ->add('updated_at')
            ->add('created_at_formatted', fn($user): string => Carbon::parse($user->updated_at)->format('d/m/Y H:i'));
    }

    public function columns(): array
    {
        return [
            Column::make('Código', 'codigo')
                ->sortable()
                ->searchable(),
            Column::make('Nombre', 'name')
                ->sortable()
                ->searchable(),
            Column::make('Descripción', 'description')
                ->sortable()
                ->searchable(),
            Column::make('Uuid', 'uuid')
                ->sortable()
                ->hidden()
                ->searchable(),
            Column::make('Creado el', 'created_at_formatted', 'updated_at')
                ->sortable()
                ->searchable(),
            Column::action('Acciónes'),
        ];
    }

    public function filters(): array
    {
        return [];
    }

    // DELETE
    #[\Livewire\Attributes\On('delete')]
    public function delete($rowId): void
    {
        $uuids = Category::where('uuid', $rowId)->pluck('uuid')->toArray();
        if (!$uuids) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Categoría no encontrada.',
            ]);
            return;
        }

        $this->dispatch('swal', [
            'icon' => 'warning',
            'title' => '¿Estás seguro de eliminar la categoría?',
            'text' => 'Esta acción no se puede deshacer.',
            'showCancelButton' => true,
            'confirmButtonText' => 'Sí, eliminar',
            'cancelButtonText' => 'Cancelar',
            'onConfirm' => "Livewire.dispatch('confirmDelete', { rowIds: " . json_encode($uuids) . ' })',
        ]);
    }

    #[\Livewire\Attributes\On('confirmDelete')]
    public function confirmDelete(array $rowIds): void
    {
        $customers = Category::whereIn('uuid', $rowIds)->get();
        if ($customers->isEmpty()) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Categoría no encontrada.',
            ]);
            return;
        }

        try {
            $customers->each->delete();
            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Eliminado',
                'text' => 'Categoría eliminada correctamente.',
            ]);
        } catch (\Exception $exception) {
            Log::error('Error al eliminar categoría: ' . $exception->getMessage());
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Ocurrió un error al eliminar la categoría.',
            ]);
        }
    }

    #[On('bulkDelete.{tableName}')]
    public function bulkDelete(): void
    {
        $uuids = Category::whereIn('uuid', $this->checkboxValues)->pluck('uuid')->toArray();
        $this->dispatch('swal', [
            'icon' => 'warning',
            'title' => '¿Estás seguro de eliminar las categorías?',
            'text' => 'Esta acción no se puede deshacer.',
            'showCancelButton' => true,
            'confirmButtonText' => 'Sí, eliminar',
            'cancelButtonText' => 'Cancelar',
            'onConfirm' => "Livewire.dispatch('confirmDelete', { rowIds: " . json_encode($uuids) . ' })',
        ]);
    }

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

        $data = Category::whereIn('uuid', $this->checkboxValues)->get();
        $headers = ['ID', 'Código', 'Nombre', 'Descripción'];

        return Excel::download(
            new GenericExport(
                data: $data,
                headers: $headers,
                mapping: function ($category): array {
                    return [
                        $category->id,
                        $category->codigo,
                        $category->name,
                        $category->description,
                    ];
                }
            ),
            'categories.xlsx'
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
        $model = Category::class;
        $headers = ['Código', 'Nombre', 'Descripción'];
        $titulo = 'Categorías';
        $columns = ['codigo', 'name', 'description'];
        $fileName = 'categories_export.pdf';
        // Enviar al componente PDF
        $this->dispatch('openPdfExport', $uuids, $model, $titulo, $columns, $headers, $fileName);
    }

    public function actions(Category $category): array
    {
        return [
            Button::add('edit')
                ->slot('Editar')
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('editCategory', ['categoryId' => $category->uuid]),
            Button::add('delete')
                ->slot('Eliminar')
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('delete', ['rowId' => $category->uuid]),
        ];
    }
}
