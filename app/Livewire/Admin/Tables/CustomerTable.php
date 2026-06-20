<?php

namespace App\Livewire\Admin\Tables;

use App\Enum\DocumentEnum;
use App\Models\Customer;
use App\Exports\GenericExport;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class CustomerTable extends PowerGridComponent
{
     public string $primaryKey = 'uuid';

    public string $sortField = 'customers.created_at';
    public string $tableName = 'customer-table-vuz6a3-table';

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
        return Customer::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('document_number')
            ->add('identity')
            ->add('identity_formatted', fn($customer): string => DocumentEnum::tryFrom(trim($customer->identity))?->label() ?? '')
            ->add('type')
            ->add('name')
            ->add('email')
            ->add('phone')
            ->add('address')
            ->add('created_at')->add('created_at_formatted', fn($user): string => Carbon::parse($user->created_at)->format('d/m/Y H:i:s'));;
    }

    public function columns(): array
    {
        return [
            Column::make('Número de documento', 'document_number')
                ->sortable()
                ->searchable(),
            Column::make('Tipo de documento', 'identity_formatted')
                ->sortable()
                ->searchable(),
            Column::make('Tipo de cliente', 'type')
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

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $customer = Customer::where('uuid', $rowId)->first();
        if (!$customer) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Cliente no encontrado.',
            ]);
            return;
        }
        redirect()->route('admin.customers.edit', $customer);
    }


     // DELETE
    #[\Livewire\Attributes\On('delete')]
    public function delete($rowId): void
    {
        $uuids = Customer::where('uuid', $rowId)->pluck('uuid')->toArray();
        if (!$uuids) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Cliente no encontrado.',
            ]);
            return;
        }

        $this->dispatch('swal', [
            'icon' => 'warning',
            'title' => '¿Estás seguro de eliminar el cliente?',
            'text' => 'Esta acción no se puede deshacer.',
            'showCancelButton' => true,
            'confirmButtonText' => 'Sí, eliminar',
            'cancelButtonText' => 'Cancelar',
            'onConfirm' => "Livewire.dispatch('confirmDelete', { rowIds: " . json_encode($uuids) . " })",
        ]);
    }

    #[\Livewire\Attributes\On('confirmDelete')]
    public function confirmDelete(array $rowIds): void
    {

        $customers = Customer::whereIn('uuid', $rowIds)->get();
        if ($customers->isEmpty()) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Cliente no encontrado.',
            ]);
            return;
        }
        try {
            $customers->each->delete();
            $this->dispatch('pg:eventRefresh-' . $this->tableName); // 👈 nuevo

            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Eliminado',
                'text' => 'Cliente eliminado correctamente.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error al eliminar cliente: ' . $e->getMessage());
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Ocurrió un error al eliminar el cliente.',
            ]);
        }
    }

    #[On('bulkDelete.{tableName}')]
    public function bulkDelete(): void
    {
        $uuids = Customer::whereIn('uuid', $this->checkboxValues)->pluck('uuid')->toArray();
        Log::info('Bulk delete requested for UUIDs: ' . implode(', ', $uuids));
        $this->dispatch('swal', [
            'icon' => 'warning',
            'title' => '¿Estás seguro de eliminar los clientes?',
            'text' => 'Esta acción no se puede deshacer.',
            'showCancelButton' => true,
            'confirmButtonText' => 'Sí, eliminar',
            'cancelButtonText' => 'Cancelar',
            'onConfirm' => "Livewire.dispatch('confirmDelete', { rowIds: " . json_encode($uuids) . " })",
        ]);
    }



    //EXPORT
    #[On('exportExcel.{tableName}')]
    public function exportExcel()
    {
        if (empty($this->checkboxValues)) {

            $this->dispatch('swal', [
                'title' => 'Precaución',
                'text' => 'No se han seleccionado registros para exportar.',
                'icon' => 'warning',
            ]);
            return;
        }

        $data = Customer::whereIn('uuid', $this->checkboxValues)->get();
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
            'Listado_Clientes.xlsx'
        );
    }

    #[On('exportPdf.{tableName}')]
    public function exportPdf(): void
    {

        if (empty($this->checkboxValues)) {

            $this->dispatch('swal', [
                'title' => 'Precaución',
                'text' => 'No se han seleccionado registros para exportar.',
                'icon' => 'warning',
            ]);
            return;
        }



        $uuids = $this->checkboxValues;
        $model = Customer::class;
        $headers = ['Tpo. documento', 'Nro. documento', 'Nombre', 'Correo', 'Teléfono', 'Dirección'];
        $titulo = 'Clientes';
        $columns = ['identity_type_label', 'document_number', 'name', 'email', 'phone', 'address'];
        $fileName = 'Listado_Clientes.pdf';
        // Enviar al componente PDF
        $this->dispatch('openPdfExport', $uuids, $model, $titulo, $columns, $headers, $fileName);
    }
    public function actions(Customer $customer): array
    {
        return [
            Button::add('edit')
                ->slot('Editar')
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit', ['rowId' => $customer->uuid]),

            Button::add('delete')
                ->slot('Eliminar')
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('delete', ['rowId' => $customer->uuid]),
        ];
    }
}
