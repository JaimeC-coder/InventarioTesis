<?php

namespace App\Livewire\Admin\Tables;

use App\Models\Purchase;
use App\Services\FileServices;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class PurchaseTable extends PowerGridComponent
{
    public string $tableName = 'purchase-table-kk6jy5-table';

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
        return Purchase::query()->with(['supplier', 'warehouse', 'purchaseOrder']);
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('voucher_type')
            ->add('voucher_type_formatted', fn($user): string => ($user->voucher_type === '1' ? 'Factura' : ($user->voucher_type === '2' ? 'Boleta' : 'Otros')))
            ->add('serie')
            ->add('correlativo')
            ->add('purchaseOrder.serie')
            ->add('purchaseOrder.serie', fn($user): string => $user->purchaseOrder ? $user->purchaseOrder->serie : 'Sin orden de compra')
            ->add('date')
            ->add('date_formatted', fn($user): string => Carbon::parse($user->date)->format('d/m/Y'))
            ->add('supplier.name')
            ->add('warehouse.name')
            ->add('total')
            ->add('observation')
            ->add('uuid')
            ->add('created_at')->add('created_at_formatted', fn($user): string => Carbon::parse($user->created_at)->format('d/m/Y H:i:s'));
        ;
    }

    public function columns(): array
    {
        return [
            Column::make('Tipo de comprobante', 'voucher_type_formatted', 'voucher_type')
                ->sortable()
                ->searchable(),
            Column::make('Serie', 'serie')
                ->sortable()
                ->searchable(),
            Column::make('Correlativo', 'correlativo')
                ->sortable()
                ->searchable(),
            Column::make('Orden de compra', 'purchaseOrder.serie')
                ->sortable()
                ->searchable(),
            Column::make('Fecha', 'date_formatted', 'date')
                ->sortable(),
            Column::make('Proveedor', 'supplier.name')
                ->sortable()
                ->searchable(),
            Column::make('Almacén', 'warehouse.name')
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

    #[\Livewire\Attributes\On('pdf')]
    public function pdf(string $rowId): void
    {
        $purchase = Purchase::whereUuid($rowId)->first();
        Log::info('GENERANDO PDF EN TABLA DE COMPRAS PARA: ', [$purchase]);
        if (is_null($purchase->file_path) || $purchase->file_path === '') {
            $model = Purchase::class;
            $payload = [
                'model' => $model,
                'uuids' => $rowId,
            ];
            $file = FileServices::generatePdfNow($payload);
            Log::info('PDF GENERADO EN TABLA DE COMPRAS: ' . $file);
            $routeFile = $file;
            $purchase->file_path = $routeFile;
            $purchase->save();
        } else {
            $routeFile = $purchase->file_path;
        }

        $routeFile = FileServices::url($routeFile);
        $this->js("
            const pdfUrl = '{$routeFile}';
            window.open(pdfUrl, '_blank');
        ");
    }

    public function actions(Purchase $purchase): array
    {
        return [
            Button::add('pdf')
                ->slot('PDF: ' . $purchase->serie)
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('pdf', ['rowId' => $purchase->uuid]),
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
