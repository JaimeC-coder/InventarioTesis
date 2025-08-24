<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class PurchaseOrderCreate extends Component
{
    public $voucher_type = 1;

    public $serie = 'OC-00001';

    public $correlativo;

    public $date = '';

    public $supplier_id = '';

    public $total = 0.00;

    public $observation = '';

    public $product_id;

    public $products = [];

    public function mount(): void
    {
        $this->correlativo = Purchase::where('serie', $this->serie)->max('correlativo') + 1;
        $this->date = now()->format('Y-m-d');
    }

    public function addProduct(): void
    {
        $this->validate([
            'product_id' => 'required|exists:products,uuid',
        ]);
        $product = Product::where('uuid', $this->product_id)->first();

        // $exists = collect($this->products)->where('id', $product->id)->first();
        // if ($exists) {
        //     //quiero enviar un flash.banner para notificar que el producto ya fue agregado
        //     $this->dispatchBrowserEvent('banner-message', [
        //         'style' => 'success',
        //         'message' => 'Producto agregado correctamente!',
        //     ]);

        //     return;
        // }

        $this->products[] = [
            'id' => $product->id,
            'name' => $product->name,
            'quantity' => 1,
            'price' => 0,
            'subtotal' => 0,
        ];

        $this->reset('product_id');
    }

    public function render()
    {
        return view('livewire.admin.purchase-order-create');
    }
}
