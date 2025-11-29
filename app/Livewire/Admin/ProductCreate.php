<?php

namespace App\Livewire\Admin;

use App\Models\Measure;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ProductCreate extends Component
{
    /**
     * Agregar una validacion para el codigo del producto
     * Que si existe el name_specific, el name no se debe de researtear
     * Que si no existe el name_specific, el name se debe de researtear
     * Al momento de agregar un producto, se debe de validar que no exista ya el codigo
     * Al momento de agregar un producto, se debe de validar que no exista ya el nombre
     * Al momento de guardar, se debe de validar que no exista ya el codigo
     */
    public $measures_uuid = [];

    public $units_uuid = [];

    public $category_uuid;

    public $name;

    public $name_specific;

    public $description;

    public $locked = false;

    public $price_sale;

    public $price_purchase;

    public $productBaseName = '';

    public $stock;

    public $alert_stock;

    public $code;

    public $stock_min = 0;

    public $category_code = 0;

    public $products = [];

    public function addProduct(): void
    {
        $this->validate([
            'category_uuid' => 'required|exists:categories,uuid',
            'category_code' => 'required',
            'code' => 'required',
            'name' => 'required',
            'name_specific' => 'nullable',
            'units_uuid' => 'required|array|min:1',
            'measures_uuid' => 'required|array|min:1',
        ]);
        $units = Unit::whereIn('uuid', $this->units_uuid)->get();
        $measures = Measure::whereIn('uuid', $this->measures_uuid)->get();
        $id = count($this->products) + 1;
        foreach ($units as $unit) {
            foreach ($measures as $measure) {
                // Concatenado: códigoUsuario-códigoUnidad-códigoMedida
                $codigoConcatenado = sprintf('%s%s%s%s', $this->category_code, $this->code, $unit->code, $measure->code);
                // Nombre: base + unidad + medida
                $nameespecificPart = $this->name_specific ? ' ' . $this->name_specific : '';
                $nombreFinal = sprintf('%s%s por %s de %s', $this->name, $nameespecificPart, $unit->name, $measure->name);
                $this->productBaseName = $this->name . $nameespecificPart;
                $this->products[] = [
                    'id' => $id++,
                    'codigo'        => $codigoConcatenado,
                    'name'          => $nombreFinal,
                    'price_sale'        => 0, // Inicialmente en 0, se puede editar en la tabla
                    'price_purchase'    => 0,
                    'unituuid'    => $unit->uuid, // Concatenado para identificar
                    'measureuuid' => $measure->uuid,
                    'unit' => $unit->name,
                    'measure' => $measure->name,
                ];
            }
        }

        $this->locked = true;
        $this->reset(['name', 'name_specific', 'units_uuid', 'measures_uuid']);
    }

    public function removeProduct($id): void
    {
        $this->products = array_filter($this->products, fn(array $product): bool => $product['id'] !== $id);
        // Reindexar el array para evitar problemas con los IDs
        $this->products = array_values($this->products);
    }

    public function saveProducts(): void
    {
        $this->validate([
            'category_code' => 'required',
            'code' => 'required',
            'description' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.codigo' => 'required|string|distinct|unique:products,barcode',
            'products.*.name' => 'required|string',
            'products.*.unituuid' => 'required|exists:units,uuid',
            'products.*.measureuuid' => 'required|exists:measures,uuid',
            'products.*.price_sale' => 'required|numeric|min:0',
            'products.*.price_purchase' => 'required|numeric|min:0',
            'category_uuid' => 'required|exists:categories,uuid',
            'stock_min' => 'nullable|integer|min:0',
            'productBaseName' => 'required|string',
        ], [
            'products.*.codigo.unique' => 'El código :input ya existe en la base de datos.',
        ]);
        try {
            $category_id = \App\Models\Category::where('uuid', $this->category_uuid)->value('id');
            $productBaseid = Product::create([
                'name' => $this->productBaseName,
                'category_code' => $this->category_code,
                'code' => $this->code,
                'description' => $this->description,
                'price_sale' => 0,
                'price_purchase' => 0,
                'stock' => 0,
                'min_stock' => $this->stock_min,
                'is_active_product' => true,
                'category_id' => $category_id,
            ]);
            // luego vamos a crear los productos dependientes
            foreach ($this->products as $product) {
                Product::create([
                    'name' => $product['name'],
                    'category_code' => $this->category_code,
                    'code' => $this->code,
                    'barcode' => $product['codigo'],
                    'description' => $this->description,
                    'price_sale' => $product['price_sale'],
                    'price_purchase' => $product['price_purchase'],
                    'stock' => 0,
                    'min_stock' => $this->stock_min,
                    'is_active_product' => true,
                    'productBase_id' => $productBaseid->id,
                    'category_id' => $category_id,
                    'unit_id' => \App\Models\Unit::where('uuid', $product['unituuid'])->value('id'),
                    'measure_id' => \App\Models\Measure::where('uuid', $product['measureuuid'])->value('id'),
                ]);
            }

            $this->dispatch('swal:success', [
                'title' => 'Productos guardados',
                'text' => 'Los productos se han guardado correctamente.',
                'icon' => 'success',
            ]);
        } catch (\Throwable $throwable) {
            Log::error('Error al guardar productos: ' . $throwable->getMessage());
            $this->dispatch('swal:success', [
                'title' => 'Error al guardar productos',
                'text' => 'Ocurrió un error al guardar los productos. Por favor, inténtelo de nuevo.',
                'icon' => 'error',
            ]);
            return;
        }

        Log::info('Guardando productos', $this->products);
        $this->products = [];
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.product-create');
    }
}
