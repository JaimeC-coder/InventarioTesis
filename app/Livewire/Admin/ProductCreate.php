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

    public $price;

    public $stock;

    public $alert_stock;

    public $code;

    public $stock_min = 0;

    public $category_code = 0;

    public $products = [];

    public function addProduct(): void
    {
        $this->validate([
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
                $codigoConcatenado = sprintf('%s-%s-%s-%s', $this->category_code, $this->code, $unit->code, $measure->code);
                // Nombre: base + unidad + medida
                $nombreFinal = sprintf('%s %s por %s de %s', $this->name, $this->name_specific, $unit->name, $measure->name);
                $this->products[] = [
                    'id' => $id++,
                    'codigo'        => $codigoConcatenado,
                    'name'          => $nombreFinal,
                    'precio'        => 0, // Inicialmente en 0, se puede editar en la tabla
                    'uuid'    => $unit->uuid . '-' . $measure->uuid, // Concatenado para identificar
                    'unit' => $unit->name,
                    'measure' => $measure->name,
                ];
            }
        }

        Log::info('Productos a crear', $this->products);
        $this->reset(['code', 'name', 'name_specific', 'units_uuid', 'measures_uuid', 'price']);
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.product-create');
    }
}
