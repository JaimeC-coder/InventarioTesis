<?php

namespace App\Livewire\admin;

use App\Models\Image;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Columns\ImageColumn;

class ProductTable extends DataTableComponent
{
    // protected $model = Product::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('created_at', 'desc');
    }

    public function columns(): array
    {
        return [
            Column::make('#')
                ->label(function ($row, Column $column): int {
                    return $this->getRowNumber();
                })
                ->sortable(),
            Column::make("Name", "name")
                ->sortable()->searchable(),

            ImageColumn::make('Image')
                ->location(fn($row) => $row->image)
                ->attributes(
                    fn($row)=>[
                        'class' => 'image-product',
                    ]
                ),
            Column::make("Category", "category.name")
                ->sortable()->searchable(),
            Column::make("Description", "description")
                ->sortable()->searchable()
                ->format(
                    fn($value, $row, Column $column) =>
                    strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value
                ),
            Column::make("Price", "price")
                ->sortable()->searchable(),
            Column::make("Uuid", "uuid")
                ->sortable()->hideIf(true),
            Column::make("Sku", "sku")
                ->sortable()->searchable()->format(
                    fn($value, $row, Column $column) =>
                    strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value
                ),
            Column::make("Barcode", "barcode")
                ->sortable()->searchable(),
            Column::make('Fecha de creación', 'created_at')
                ->sortable(),

            Column::make('Acciones')
                ->label(function ($row, Column $column) {
                    // $row aquí es el modelo completo Product
                    return view('admin.products.actions', [
                        'product' => $row, // Pasa el modelo completo
                    ]);
                }),
        ];
    }
    protected function getRowNumber(): int
    {
        static $position = null;
        if ($position === null) {
            $position = (($this->getPage() - 1) * $this->getPerPage()) + 1;
        } else {
            $position++;
        }

        return $position;
    }
    public function builder(): Builder
    {
        return Product::query()->with(['category', 'images']);
    }
}
