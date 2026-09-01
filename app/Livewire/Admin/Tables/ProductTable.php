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
            PowerGrid::detail()
                ->view('components.product-detail')
                ->params(['tableName' => $this->tableName])
                ->collapseOthers()
                ->showCollapseIcon(),
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
        return Product::whereNotNull('product_base_id')
            ->with(['category', 'unit', 'measure', 'productBase', 'stockByWarehouse.warehouse']);
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
            ->add('price_sale_regular')
            ->add('price_sale_a1')
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
                ->searchable()
                ->visibleInExport(visible: true),
            Column::make('Nombre', 'name')
                ->sortable()
                ->searchable()
                ->visibleInExport(visible: true),
            Column::make('Código de barras', 'code')
                ->sortable()
                ->searchable()
                ->visibleInExport(visible: true),
            Column::make('Precio de compra', 'price_purchase')
                ->sortable()
                ->searchable()
                ->visibleInExport(visible: true),
            Column::make('Precio de general', 'price_sale_regular')
                ->sortable()
                ->searchable()
                ->visibleInExport(visible: true),
            Column::make('Precio de A1', 'price_sale_a1')
                ->sortable()
                ->searchable()
                ->visibleInExport(visible: true),
            // Column::make('Uuid', 'uuid')
            //     ->sortable()
            //
            Column::make('Category', 'category_name')
                ->sortable()
                ->searchable()
                ->visibleInExport(visible: true),
            Column::make('Unidad', 'unit_name')
                ->sortable()
                ->searchable()
                ->visibleInExport(visible: true),
            Column::make('Medida', 'measure_name')
                ->sortable()
                ->searchable()
                ->visibleInExport(visible: true),
            Column::make('Producto base', 'productBase_name')
                ->sortable()
                ->searchable()
                ->visibleInExport(visible: true),
            Column::make('Stock mínimo', 'min_stock')
                ->sortable()
                ->searchable()
                ->visibleInExport(visible: true),
            Column::make('Stock actual', 'stock')
                ->sortable()
                ->searchable()
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
        $this->dispatch('swal:confirmDelete', [
            'title' => '¿Estás seguro?',
            'text' => 'Esta acción no se puede deshacer.',
            'icon' => 'warning',
            'confirmButtonText' => 'Sí, eliminar',
            'cancelButtonText' => 'Cancelar',
            'productId' => $params,
        ]);
    }

    public function delete(string $productId): void
    {
        $product = Product::where('uuid', $productId)->first();
        if ($product) {
            $product->delete();
            $this->dispatch('pg:eventRefresh-' . $this->tableName);
            $this->resetPage();
            $this->dispatch('swal:success', [
                'title' => 'Eliminado',
                'text' => 'El producto se eliminó correctamente.',
                'icon' => 'success',
            ]);
        }
    }

    #[On('bulkDelete.{tableName}')]
    public function bulkDelete(): void
    {
        Product::whereIn('uuid', $this->checkboxValues)->delete();
        $this->dispatch('pg:eventRefresh-' . $this->tableName);
        $this->resetPage();
        $this->dispatch('swal:success', [
            'title' => 'Eliminado',
            'text' => 'Los productos seleccionados se eliminaron correctamente.',
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
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('confirmDelete', ['params' => $product->uuid]),
        ];
    }
}
