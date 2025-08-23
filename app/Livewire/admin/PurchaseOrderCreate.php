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
        Log::info('Producto agregado', ['product_id' => $this]);
        $this->validate([
            'product_id' => 'required|exists:products,id',
        ]);
        $product = Product::where('uuid', $this->product_id)->first();
        $this->products[] = $product;
        Log::info('Producto agregado', ['product' => $product]);
        $this->reset('product_id');
    }

    public function render()
    {
        return view('livewire.admin.purchase-order-create');
    }
}
