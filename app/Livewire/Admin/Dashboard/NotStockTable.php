<?php

namespace App\Livewire\Admin\Dashboard;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class NotStockTable extends PowerGridComponent
{
    public string $tableName = 'not-stock-table-zcak7j-table';

    public string $primaryKey = 'records.id';

    public ?int $lockedWarehouseId = null;

    public function setUp(): array
    {
        $this->showCheckBox(); // valor = records.id

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
                ->slot('Crear orden de compra (<span x-text="window.pgBulkActions.count(\'' . $this->tableName . '\')"></span>)')
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('bulkDelete.' . $this->tableName, []),
            Button::add('bulk-delete')
                ->slot('Limpiar elementos seleccionados')
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('bulkClear.' . $this->tableName, []),
        ];
    }

    public function datasource(): ?Builder
    {
        return DB::table('records')
            ->join('products', 'records.product_id', '=', 'products.id')
            ->select(
                'records.id as id',
                'products.name as product',
                'records.quantity as quantity',
                'records.warehouse_name as warehouse_name',
                'records.warehouse_id as warehouse_id'
            )
            ->whereColumn('records.quantity', '<=', 'products.min_stock')
            ->orderBy('records.warehouse_id', 'asc');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('warehouse_id')
            ->add('product')
            ->add('quantity')
            ->add('warehouse_name');
    }

    public function columns(): array
    {
        return [
            Column::make('id', 'id'),
            Column::make('producto', 'product'),
            Column::make('cantidad', 'quantity'),
            Column::make('almacen', 'warehouse_name'),
        ];
    }

    public function filters(): array
    {
        return [];
    }

    #[On('bulkDelete.{tableName}')]
    public function bulkDelete(): never
    {
        dd($this->checkboxValues);
        // Product::whereIn('uuid', $this->checkboxValues)->delete();
        // $this->dispatch('pg:eventRefresh-' . $this->tableName);
        // $this->resetPage();
        // $this->dispatch('swal:success', [
        //     'title' => 'Eliminado',
        //     'text' => 'Los productos seleccionados se eliminaron correctamente.',
        //     'icon' => 'success',
        // ]);
        // regresamos al inicio de la tabla
        //
    }

    #[On('bulkClear.{tableName}')]
    public function bulkClear(): void
    {
        $this->checkboxValues = [];
        $this->lockedWarehouseId = null;
        $this->dispatch('pgBulkActions::clear', $this->tableName);
    }

    public function updatedCheckboxValues(): void
    {
        if ($this->checkboxValues === []) {
            $this->lockedWarehouseId = null;
            return;
        }

        $this->lockedWarehouseId ??= DB::table('records')
            ->whereIn('id', $this->checkboxValues)
            ->value('warehouse_id');
    }

    public function actionRules(): array
    {
        return [
            Rule::checkbox()
                ->when(fn($r): bool => $this->lockedWarehouseId !== null
                    && (int) $r->warehouse_id !== $this->lockedWarehouseId)
                ->hide(),
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
