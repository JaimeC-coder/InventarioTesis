<?php

namespace App\Livewire\Admin\Tables;

use App\Exports\GenericExport;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use Maatwebsite\Excel\Facades\Excel;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class ProductTable extends PowerGridComponent
{
    public string $primaryKey = 'uuid';

    public string $sortField = 'products.created_at';

    public string $tableName = 'product-table-dwonrg-table';

    public function setUp(): array
    {
        $this->showCheckBox('uuid');

        return [
            PowerGrid::header()
                ->showToggleColumns()
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
        return Product::where('productBase_id', '!=', null)
            ->with(['category', 'unit', 'measure', 'productBase']);
    }

    public function relationSearch(): array
    {
        return [
            'category' => ['name'],
            'unit' => ['name'],
            'measure' => ['name'],
            'productBase' => ['name'],
        ];
    }

    public function fields(): PowerGridFields
    {
        $barcodeGeneratorPNG = new \Picqer\Barcode\BarcodeGeneratorPNG();
        return PowerGrid::fields()
            ->add('codigo')
            ->add('name')
            ->add('code', function (Product $product) use ($barcodeGeneratorPNG): string {
                return sprintf(
                    '<img src="data:image/png;base64,%s">',
                    base64_encode($barcodeGeneratorPNG->getBarcode($product->barcode, $barcodeGeneratorPNG::TYPE_CODE_128))
                );
            })
            ->add('price_purchase')
            ->add('price_sale')
            ->add('category_name', fn(Product $product) => $product->category?->name)
            ->add('unit_name', fn(Product $product) => $product->unit?->name)
            ->add('measure_name', fn(Product $product) => $product->measure?->name)
            ->add('productBase_name', fn(Product $product) => $product->productBase?->name)
            ->add('stock')
            ->add('min_stock')
            ->add('created_at')->add('created_at_formatted', fn($user): string => Carbon::parse($user->created_at)->format('d/m/Y H:i:s'));
        ;
    }

    public function columns(): array
    {
        return [
            Column::make('Código', 'barcode')
                ->sortable()
                ->visibleInExport(visible: true),
            Column::make('Nombre', 'name')
                ->sortable()
                ->visibleInExport(visible: true),
            Column::make('Código de barras', 'code')
                ->sortable()
                ->visibleInExport(visible: true),
            Column::make('Precio de compra', 'price_purchase')
                ->sortable()
                ->visibleInExport(visible: true),
            Column::make('Precio de venta', 'price_sale')
                ->sortable()
                ->visibleInExport(visible: true),
            // Column::make('Uuid', 'uuid')
            //     ->sortable()
            //
            Column::make('Category', 'category_name')
                ->sortable()
                ->visibleInExport(visible: true),
            Column::make('Unidad', 'unit_name')
                ->sortable()
                ->visibleInExport(visible: true),
            Column::make('Medida', 'measure_name')
                ->sortable()
                ->visibleInExport(visible: true),
            Column::make('Producto base', 'productBase_name')
                ->sortable()
                ->visibleInExport(visible: true),
            Column::make('Stock mínimo', 'min_stock')
                ->sortable()
                ->visibleInExport(visible: true),
            Column::make('Stock actual', 'stock')
                ->sortable()
                ->visibleInExport(visible: true),
            Column::make('Creado el', 'created_at_formatted', 'created_at')
                ->sortable()
                ->visibleInExport(visible: true),
            Column::action('Acciónes'),
        ];
    }

    public function filters(): array
    {
        return [];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit(string $rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    #[\Livewire\Attributes\On('delete')]
    public function delete(string $rowId): void
    {
        Product::find($rowId)?->delete();
        $this->emit('pg:eventRefresh-' . $this->tableName);
    }

    #[\Livewire\Attributes\On('priceupdate')]
    public function priceupdate(string $rowId): void
    {
        $this->js("Swal.fire({
                        title: '¿Estás seguro?',
                        text: 'Esta acción no se puede deshacer.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });");
    }

    #[On('bulkDelete.{tableName}')]
    public function bulkDelete(): void
    {
        Product::whereIn('uuid', $this->checkboxValues)->delete();
        $this->dispatch('pg:eventRefresh-' . $this->tableName);
        $this->resetPage();
        $this->dispatch('swal:success', [
            'title' => 'Eliminado',
            'text' => 'Las categorías seleccionadas se eliminaron correctamente.',
            'icon' => 'success',
        ]);
        // regresamos al inicio de la tabla
        //
    }

    #[On('exportExcel.{tableName}')]
    public function exportExcel()
    {
        $data = Product::whereIn('uuid', $this->checkboxValues)->get();
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
        $model = Product::class;
        $payload = [
            'model' => $model,
            'uuids' => $uuids,
        ];
        // Enviar al componente PDF
        $this->dispatch('openPdfExport', $payload);
    }

    public function actions(Product $product): array
    {
        return [
            Button::add('edit')
                ->slot('Editar')
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('editProduct', ['productuuid' => $product->uuid]),
            Button::add('priceupdate')
                ->slot('Actualizar precio')
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('updateprice', ['productuuid' => $product->uuid]),
            Button::add('delete')
                ->slot('Eliminar')
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('delete', ['rowId' => $product->id]),
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
