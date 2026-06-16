<?php

namespace App\Livewire\Admin\Tables;

use App\Exports\GenericExport;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use Maatwebsite\Excel\Facades\Excel;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class SupplierTable extends PowerGridComponent
{
    public string $primaryKey = 'uuid';

    public string $sortField = 'suppliers.created_at';

    public string $tableName = 'supplier-table-cma7wo-table';

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
        return Supplier::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('identity')
            ->add('document_number')
            ->add('name')
            ->add('email')
            ->add('phone')
            ->add('address')
            ->add('uuid')
            ->add('created_at')->add('created_at_formatted', fn($user): string => Carbon::parse($user->created_at)->format('d/m/Y H:i:s'));
        ;
    }

    public function columns(): array
    {
        return [
            Column::make('Tipo de documento', 'identity')
                ->sortable()
                ->searchable(),
            Column::make('Número de documento', 'document_number')
                ->sortable()
                ->searchable(),
            Column::make('Nombre', 'name')
                ->sortable()
                ->searchable(),
            Column::make('Correo electrónico', 'email')
                ->sortable()
                ->searchable(),
            Column::make('Teléfono', 'phone')
                ->sortable()
                ->searchable(),
            Column::make('Dirección', 'address')
                ->sortable()
                ->searchable(),
            Column::make('Uuid', 'uuid')
                ->sortable()
                ->hidden()
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

    protected function getListeners()
    {
        return [
            'confirmDelete',
            'deleteConfirmed' => 'delete', // Evento que se lanza desde JS cuando el usuario confirma
        ];
    }

    #[\Livewire\Attributes\On('confirmDelete')]
    public function confirmDelete(string $params): void
    {
        //   dd($params);
        //  $categoryId = $params['categoryId'] ?? null;
        // Emitimos un evento de JS (usaremos JS para mostrar Swal)
        $this->dispatch('swal:confirmDelete', [
            'title' => '¿Estás seguro?',
            'text' => 'Esta acción no se puede deshacer.',
            'icon' => 'warning',
            'confirmButtonText' => 'Sí, eliminar',
            'cancelButtonText' => 'Cancelar',
            'supplierId' => $params,
        ]);
    }

    public function delete($supplierId): void
    {
        $supplier = Supplier::where('uuid', $supplierId)->first();
        if ($supplier) {
            $supplier->delete();
            $this->dispatch('pg:eventRefresh-' . $this->tableName);
            $this->dispatch('swal:success', [
                'title' => 'Eliminado',
                'text' => 'El proveedor se eliminó correctamente.',
                'icon' => 'success',
            ]);
        }
    }

    #[On('bulkDelete.{tableName}')]
    public function bulkDelete(): void
    {
        Supplier::whereIn('uuid', $this->checkboxValues)->delete();
        $this->dispatch('pg:eventRefresh-' . $this->tableName);
        $this->resetPage();
        $this->dispatch('swal:success', [
            'title' => 'Eliminado',
            'text' => 'Los proveedores seleccionados se eliminaron correctamente.',
            'icon' => 'success',
        ]);
        //
    }

    #[On('exportExcel.{tableName}')]
    public function exportExcel()
    {
        $data = Supplier::whereIn('uuid', $this->checkboxValues)->get();
        $headers = ['ID', 'name'];

        return Excel::download(
            new GenericExport(
                data: $data,
                headers: $headers,
                mapping: function ($category): array {
                    return [
                        $category->id,
                        $category->name,
                    ];
                }
            ),
            'categories.xlsx'
        );
    }

    #[On('exportPdf.{tableName}')]
    public function exportPdf(): void
    {
        $uuids = $this->checkboxValues;
        $model = Supplier::class;
        $payload = [
            'model' => $model,
            'uuids' => $uuids,
        ];
        // Enviar al componente PDF
        $this->dispatch('openPdfExport', $payload);
    }

    public function actions(Supplier $supplier): array
    {
        return [
            Button::add('edit')
                ->slot('Editar')
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('editSupplier', ['supplierId' => $supplier->uuid]),
            Button::add('delete')
                ->slot('Eliminar')
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('confirmDelete', ['params' => $supplier->uuid]),
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
