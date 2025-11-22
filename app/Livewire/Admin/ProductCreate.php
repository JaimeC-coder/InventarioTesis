<?php

namespace App\Livewire\Admin;

use App\Models\Measure;
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
                $nombreFinal = sprintf('%s %s por %s de %s', $this->name, $this->name_specific, $unit->name, $measure->name);
                $this->productBaseName = $this->name . ' ' . $this->name_specific;
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

        Log::info('Productos a crear', $this->products);
        $this->locked = true;
        $this->reset([ 'name', 'name_specific', 'units_uuid', 'measures_uuid']);
    }

    public function removeProduct($id): void
    {
        $this->products = array_filter($this->products, fn(array $product): bool => $product['id'] !== $id);
        // Reindexar el array para evitar problemas con los IDs
        $this->products = array_values($this->products);
    }

    public function saveProducts(): void
    {
        // dd($this);
        // Primero vamos a validar el array de productos
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
        ]);
        /*

        name ,category_code ,code ,category_uuid , description

        // para registrar un producto dependiente seria ya necesario
        // name ,category_code ,code ,barcode, description,category_uuid, unituuid, measureuuid, price_sale, price_purchase, stock, min_stock , product_base_id
        */
        Log::info('Guardando productos', $this->products);
        $this->products = [];
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.product-create');
    }
}
