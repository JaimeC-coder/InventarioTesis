<?php

namespace App\Livewire\Products;

use Livewire\Component;

class EditProduct extends Component
{
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.products.edit-product');
    }
}
