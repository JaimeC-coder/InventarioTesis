<?php

namespace App\Livewire\Admin\Dashboard;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
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
                'products.min_stock',
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
            ->add('warehouse_name')
            ->add('min_stock');
    }

    public function columns(): array
    {
        return [
            Column::make('id', 'id'),
            Column::make('producto', 'product'),
            Column::make('cantidad', 'quantity'),
            Column::make('stock minimo', 'min_stock'),
            Column::make('almacen', 'warehouse_name'),
        ];
    }

    public function filters(): array
    {
        return [];
    }

    #[On('bulkDelete.{tableName}')]
    public function bulkDelete(): null|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse //: \Illuminate\Http\RedirectResponse
    {
        if ($this->checkboxValues === []) {
            // mostrar error / return temprano
            return null;
        }

        $warehouseId = DB::table('records')
            ->join('products', 'records.product_id', '=', 'products.id')
            ->join('warehouses', 'records.warehouse_id', '=', 'warehouses.id')
            ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
            ->whereIn('records.id', $this->checkboxValues)
            ->select('warehouses.uuid as warehouse_uuid', 'suppliers.uuid as supplier_uuid')
            ->first();
        $productIds = DB::table('records')
            ->whereIn('id', $this->checkboxValues)
            ->pluck('product_id')
            ->all();
        $token = (string) Str::uuid();
        $expiresAt = now()->addHours(48);
        Cache::put(
            'low-stock-report:' . $token,
            [
                'warehouse_uuid' => $warehouseId->warehouse_uuid,
                'supplier_uuid' => $warehouseId->supplier_uuid,
                'product_ids' =>  $productIds,
            ],
            $expiresAt
        );
        $token = URL::temporarySignedRoute(
            'admin.purchases.from-report',
            $expiresAt,
            ['token' => $token]
        );
        return redirect($token);
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
}
