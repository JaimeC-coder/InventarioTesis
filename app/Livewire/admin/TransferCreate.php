<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use App\Models\Transfer;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class TransferCreate extends Component
{
    public $type = 1;

    public $serie = 'TR-00001';

    public $correlativo;

    public $date = '';

    public $observaciones = '';

    public $total = 0.00;

    public $origin_warehouse_id = '';

    public $origin_warehouse_uuid = '';

    public $destination_warehouse_id = '';

    public $destination_warehouse_uuid = '';

    public $product_uuid = '';

    public $product_id;

    public $products = [];

    public function boot(): void
    {
        $this->withValidator(function ($validator): void {
            if ($validator->fails()) {
                $html = "<ul class='text-left'>";
                foreach ($validator->errors()->toArray() as $error) {
                    $html .= sprintf('<li>%s</li>', $error[0]);
                }

                $html .= '</ul>';
                Log::error('Validation errors: ' . $html);
                $this->dispatch('swal', [
                    'icon' => 'error',
                    'title' => 'Error',
                    'html' => $html,
                ]);
            }

            // $validator->after(function ($validator) {
            //     $total = 0;
            //     foreach ($this->products as $product) {
            //         $total += $product['quantity'] * $product['price'];
            //     }
            //     $this->total = $total;
            // });
        });
    }

    // public function updated($property, $value): void
    // {
    //     if ($property === 'type') {
    //         $this->reset('reason_uuid');
    //     }
    // }

    public function mount(): void
    {
        $this->correlativo = Transfer::where('serie', $this->serie)->max('correlativo') + 1;
        $this->date = now()->format('Y-m-d');
    }

    public function addProduct(): void
    {
        $this->validate([
            'product_uuid' => 'required|exists:products,uuid',
        ]);
        $product = Product::where('uuid', $this->product_uuid)->first();
        $exists = collect($this->products)->where('id', $product->id)->first();
        if ($exists) {
            $this->dispatch('swal', [
                'icon' => 'warning',
                'title' => 'Producto ya agregado',
                'text' => 'El producto ya ha sido agregado a la lista.',
            ]);
            $this->reset('product_id');
            return;
        }

        $this->products[] = [
            'id' => $product->id,
            'name' => $product->name,
            'quantity' => 1,
            'price' => $product->price,
            'subtotal' => 0,
        ];
        $this->reset('product_uuid');
    }

    public function save()
    {
        if (!empty($this->origin_warehouse_uuid)) {
            $this->origin_warehouse_id = Warehouse::where('uuid', $this->origin_warehouse_uuid)->value('id');
        }

        if (!empty($this->destination_warehouse_uuid)) {
            $this->destination_warehouse_id = Warehouse::where('uuid', $this->destination_warehouse_uuid)->value('id');
        }

        $this->validate([
            'type' => 'required|in:1,2',
            'serie' => 'required|string|max:20',
            'correlativo' => 'required|integer|min:1',
            'date' => 'required|date',
            'origin_warehouse_id' => 'required|exists:warehouses,id',
            'destination_warehouse_id' => 'required|exists:warehouses,id|different:origin_warehouse_id',
            'total' => 'required|numeric|min:0.01',
            'observaciones' => 'nullable|string|max:500',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
        ], [], [
            'type' => 'Tipo de movimiento',
            'serie' => 'Serie',
            'correlativo' => 'Correlativo',
            'date' => 'Fecha',
            'origin_warehouse_id' => 'Almacén de origen',
            'destination_warehouse_id' => 'Almacén de destino',
            'total' => 'Total',
            'observaciones' => 'Observaciones',
            'products.*.id' => 'ID del producto',
            'products.*.quantity' => 'Cantidad del producto',
            'products.*.price' => 'Precio del producto',
        ]);
        //quiero que esto se tenga en una transacción
        $Movement = Transfer::create([
            'type' => $this->type,
            'serie' => $this->serie,
            'correlativo' => $this->correlativo,
            'date' => $this->date,
            'origin_warehouse_id' => $this->origin_warehouse_id,
            'destination_warehouse_id' => $this->destination_warehouse_id,
            'total' => $this->total,
            'observaciones' => $this->observaciones,
        ]);
        foreach ($this->products as $product) {
            $product_id = Product::where('id', $product['id'])->value('id');
            $Movement->products()->attach($product_id, [
                'quantity' => $product['quantity'],
                'price' => $product['price'],
                'subtotal' => $product['quantity'] * $product['price'],
            ]);
        }

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Movimiento creado',
            'text' => 'El movimiento se ha creado exitosamente.',
        ]);

        return redirect()->route('admin.transfers.index');
    }

    public function render()
    {
        return view('livewire.admin.transfer-create');
    }
}
