<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class CustomerCreate extends Component
{
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.customer-create');
    }
}
