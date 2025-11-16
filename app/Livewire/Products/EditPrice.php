<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;

class EditPrice extends Component
{
    public $productId;

    public $name;

    public $price_sale;

    public $price_purchase;

    public $showModal = false;

    #[\Livewire\Attributes\On('updateprice')]
    public function loadProduct($productId): void
    {
        $product = Product::find($productId);
        if ($product) {
            $this->productId = $product->id;
            $this->name = $product->name;
            $this->price_sale = $product->price_sale;
            $this->price_purchase = $product->price_purchase;
            $this->showModal = true;
        }
    }

    public function save(): void
    {
        $this->validate([
            'price_sale' => 'nullable|numeric|min:1',
            'price_purchase' => 'nullable|numeric|min:1',
        ]);
        $product = Product::find($this->productId);
        if ($product) {
            $product->update([
                'price_sale' => $this->price_sale,
                'price_purchase' => $this->price_purchase,
            ]);
            $this->showModal = false;
            $this->dispatch('pg:eventRefresh-product-table-dwonrg-table'); // refresca tabla PowerGrid
        }
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.products.edit-price');
    }
}
