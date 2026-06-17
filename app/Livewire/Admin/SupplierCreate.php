<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class SupplierCreate extends Component
{
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.supplier-create');
    }
}
