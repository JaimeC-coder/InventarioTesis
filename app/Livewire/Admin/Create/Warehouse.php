<?php

namespace App\Livewire\Admin\Create;

use Livewire\Component;

class Warehouse extends Component
{
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.create.warehouse');
    }
}
