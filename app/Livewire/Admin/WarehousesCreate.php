<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class WarehousesCreate extends Component
{
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.warehouses-create');
    }
}
