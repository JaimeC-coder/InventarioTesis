<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;

class EditPrice extends Component
{
    public $productuuid;

    public $name;

    public $price_sale;

    public $price_purchase;

    public $showModal = false;

    #[\Livewire\Attributes\On('updateprice')]
    public function loadProduct($productuuid): void
    {
        $product = Product::where('uuid', $productuuid)->first();
        if ($product) {
            $this->productuuid = $product->uuid;
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
        $product = Product::where('uuid', $this->productuuid)->first();
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
