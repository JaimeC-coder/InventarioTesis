<?php

namespace App\Livewire\Admin\Edit;

use Livewire\Component;

class Product extends Component
{
    public $productuuid;

    public $name;

    public $code;

    public $category_code;

    public $barcode;

    public $description;

    public $price_sale_regular;

    public $price_sale_a1;

    public $price_purchase;

    public $stock;

    public $min_stock;

    public $is_active_product;

    public $productBase_uuid;

    public $uuid;

    public $category_uuid;

    public $unit_uuid;

    public $measure_uuid;

    public $showModalProduct = false;

    public $price_sale_a1_final;

    public $price_sale_regular_final;

    #[\Livewire\Attributes\On('editProduct')]
    public function loadProduct($productuuid): void
    {
        $this->resetErrorBag();
        $product = \App\Models\Product::where('uuid', $productuuid)->first();
        // dd($product);
        if ($product) {
            $this->productuuid = $product->uuid;
            $this->name = $product->name;
            $this->code = $product->code;
            $this->category_code = $product->category_code;
            $this->barcode = $product->barcode;
            $this->description = $product->description;
            $this->price_sale_regular = $product->price_sale_regular;
            $this->price_sale_a1 = $product->price_sale_a1;
            $this->price_purchase = $product->price_purchase;
            $this->stock = $product->stock;
            $this->min_stock = $product->min_stock;
            $this->is_active_product = $product->is_active_product;
            $this->productBase_uuid = $product->productBase->uuid ?? null;
            $this->uuid = $product->uuid;
            $this->category_uuid = $product->category->uuid ?? null;
            $this->unit_uuid = $product->unit->uuid ?? null;
            $this->measure_uuid = $product->measure->uuid ?? null;
            $this->showModalProduct = true;
        }
    }

    public function saveProduct(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'category_code' => 'nullable|string|max:255',
            'barcode' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_sale_regular' => 'required|numeric|min:0',
            'price_sale_a1' => 'required|numeric|min:0',
            'price_purchase' => 'required|numeric|min:0',
            'category_uuid' => 'required|exists:categories,uuid',
            'unit_uuid' => 'required|exists:units,uuid',
            'productBase_uuid' => 'nullable|exists:products,uuid',
            'measure_uuid' => 'required|exists:measures,uuid',
            'min_stock' => 'required|integer|min:0',
        ]);
        $product = \App\Models\Product::where('uuid', $this->productuuid)->firstOrFail();
        $category = \App\Models\Category::where('uuid', $this->category_uuid)->first();
        // Datos base (siempre se actualizan)
        $baseData = [
            'name'              => $this->name,
            'description'       => $this->description,
            'price_sale_regular'        => $this->price_sale_regular,
            'price_sale_a1'        => $this->price_sale_a1,
            'price_purchase'    => $this->price_purchase,
            'min_stock'         => $this->min_stock,
            'category_id'       => $category->id,
        ];
        // Verificar si cambian los datos que afectan el barcode o relaciones
        $hasChanges = (
            $product->code          !== $this->code ||
            $product->category_code !== $this->category_code ||
            $product->unit->uuid    !== $this->unit_uuid ||
            $product->measure->uuid !== $this->measure_uuid
        );
        if ($hasChanges) {
            // Obtener IDs reales
            $unit_id =  \App\Models\Unit::where('uuid', $this->unit_uuid)->value('id');
            $unit_code = \App\Models\Unit::where('uuid', $this->unit_uuid)->value('code');
            $unit_name = \App\Models\Unit::where('uuid', $this->unit_uuid)->value('name');
            $measure_id = \App\Models\Measure::where('uuid', $this->measure_uuid)->value('id');
            $measure_code = \App\Models\Measure::where('uuid', $this->measure_uuid)->value('code');
            $measure_name = \App\Models\Measure::where('uuid', $this->measure_uuid)->value('name');
            $productBase_name  = \App\Models\Product::where('uuid', $this->productBase_uuid)->value('name');
            $productBase_code  = \App\Models\Product::where('uuid', $this->productBase_uuid)->value('code');
            // Generar nuevo barcode
            $barcode = $this->code . $this->category_code . $unit_code . $measure_code;
            $name =  sprintf('%s por %s de %s', $this->clearName($productBase_name, $productBase_code), $unit_name, $measure_name);
            $extraData = [
                'name'              => strtoupper($name),
                'code'          => $this->code,
                'category_code' => $this->category_code,
                'barcode'       => $barcode,
                'unit_id'       => $unit_id,
                'measure_id'    => $measure_id,
            ];
        } else {
            $extraData = [];
        }

        // Actualizar producto
        $product->update(array_merge($baseData, $extraData));
        $this->showModalProduct = false;
        $this->dispatch('pg:eventRefresh-product-table-dwonrg-table'); // refresca tabla PowerGrid
    }

    protected function clearName(string $name, String $codigo): string
    {
        // busca y elimina el valor de $codigo en $name
        $name = str_ireplace($codigo, '', $name);
        // elimina espacios en blanco al inicio y al final
        $name = trim($name);
        return $name;
    }

    public function editPrice(): void
    {
        if (empty($this->price_sale_a1_final)) {
            $this->price_sale_a1_final = 0;
        }

        if (empty($this->price_sale_regular_final)) {
            $this->price_sale_regular_final = 0;
        }

        $this->price_sale_a1 = round($this->price_sale_a1_final / 1.18, 6);
        $this->price_sale_regular = round($this->price_sale_regular_final / 1.18, 6);
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.edit.product');
    }
}
