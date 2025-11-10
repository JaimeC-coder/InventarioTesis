<?php

namespace App\Livewire\Admin\Tables;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class ProductTable extends PowerGridComponent
{
    public string $tableName = 'product-table-dwonrg-table';

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
        return Product::query()->with(['category']);
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        $barcodeGeneratorPNG = new \Picqer\Barcode\BarcodeGeneratorPNG();
        return PowerGrid::fields()
            ->add('name')
            ->add('barcode', function (Product $product) use ($barcodeGeneratorPNG): string {
                return sprintf(
                    '<img src="data:image/png;base64,%s">',
                    base64_encode($barcodeGeneratorPNG->getBarcode($product->id, $barcodeGeneratorPNG::TYPE_CODE_128))
                );
            })
            ->add('price')
            ->add('category.name')
            ->add('stock')
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')
                ->sortable()
                ->searchable(),
            Column::make('Barcode', 'barcode')
                ->sortable()
                ->searchable(),
            Column::make('Price', 'price')
                ->sortable()
                ->searchable(),
            Column::make('Uuid', 'uuid')
                ->sortable()
                ->searchable(),
            Column::make('Category id', 'category.name')
                ->sortable()
                ->searchable(),
            Column::make('Stock', 'stock')
                ->sortable()
                ->searchable(),
            Column::make('Created at', 'created_at')
                ->sortable()
                ->searchable(),
            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit(string $rowId): void
    {
        $this->js('alert('.$rowId.')');
    }

    #[\Livewire\Attributes\On('delete')]
    public function delete(string $rowId): void
    {
        Product::find($rowId)?->delete();
        $this->emit('pg:eventRefresh-'.$this->tableName);
    }

    public function actions(Product $product): array
    {
        return [
            Button::add('edit')
                ->slot('Edit: '.$product->id)
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit', ['rowId' => $product->id]),
            Button::add('delete')
                ->slot('Delete: '.$product->id)
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
