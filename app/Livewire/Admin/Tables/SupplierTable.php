<?php

namespace App\Livewire\Admin\Tables;

use App\Enum\DocumentEnum;
use App\Exports\GenericExport;
use App\Models\Supplier;
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
            ->add('identity_formatted', fn($supplier): string => DocumentEnum::tryFrom(trim($supplier->identity))?->label() ?? '')
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
            Column::make('Tipo de documento', 'identity_formatted')
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

    #[\Livewire\Attributes\On('editSupplier')]
    public function edit($supplierId): void
    {
        $supplier = Supplier::where('uuid', $supplierId)->first();
        if (!$supplier) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Proveedor no encontrado.',
            ]);
            return;
        }

        redirect()->route('admin.suppliers.edit', $supplier);
    }

    // DELETE
    #[\Livewire\Attributes\On('delete')]
    public function delete($rowId): void
    {
        $uuids = Supplier::where('uuid', $rowId)->pluck('uuid')->toArray();
        if (!$uuids) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Proveedor no encontrado.',
            ]);
            return;
        }

        $this->dispatch('swal', [
            'icon' => 'warning',
            'title' => '¿Estás seguro de eliminar el proveedor?',
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
        $customers = Supplier::whereIn('uuid', $rowIds)->get();
        if ($customers->isEmpty()) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Proveedor no encontrado.',
            ]);
            return;
        }

        try {
            $customers->each->delete();
            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Eliminado',
                'text' => 'Proveedor eliminado correctamente.',
            ]);
            $this->dispatch('pg:eventRefresh-' . $this->tableName); // 👈 nuevo
        } catch (\Exception $exception) {
            Log::error('Error al eliminar proveedor: ' . $exception->getMessage());
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Ocurrió un error al eliminar el proveedor.',
            ]);
        }
    }

    #[On('bulkDelete.{tableName}')]
    public function bulkDelete(): void
    {
        $uuids = Supplier::whereIn('uuid', $this->checkboxValues)->pluck('uuid')->toArray();
        $this->dispatch('swal', [
            'icon' => 'warning',
            'title' => '¿Estás seguro de eliminar los proveedores?',
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

        $data = Supplier::whereIn('uuid', $this->checkboxValues)->get();
        $headers = ['ID', 'Tipo de documento', 'Número de documento', 'Nombre', 'Correo electrónico', 'Teléfono', 'Dirección'];

        return Excel::download(
            new GenericExport(
                data: $data,
                headers: $headers,
                mapping: function ($category): array {
                    return [
                        $category->id,
                        $category->identity_type_label,
                        $category->document_number,
                        $category->name,
                        $category->email,
                        $category->phone,
                        $category->address,
                    ];
                }
            ),
            'Listado_Proveedores.xlsx'
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
        $model = Supplier::class;
        $headers = ['Tpo. documento', 'Nro. documento', 'Nombre', 'Correo', 'Teléfono', 'Dirección'];
        $titulo = 'Proveedores';
        $columns = ['identity_type_label', 'document_number', 'name', 'email', 'phone', 'address'];
        $fileName = 'Listado_Proveedores.pdf';
        // Enviar al componente PDF
        $this->dispatch('openPdfExport', $uuids, $model, $titulo, $columns, $headers, $fileName);
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
                ->dispatch('delete', ['rowId' => $supplier->uuid]),
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
