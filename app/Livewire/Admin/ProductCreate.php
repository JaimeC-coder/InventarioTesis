<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class ProductCreate extends Component
{
    public $measures_uuid;
    public $units_uuid;
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

    public function addProduct(): void{
        dd($this);

    }




    public function render()
    {
        return view('livewire.admin.product-create');
    }
}
