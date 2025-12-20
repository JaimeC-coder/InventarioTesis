<?php

namespace App\Livewire\Modal;

use Livewire\Component;

class EditPurchasesOrders extends Component
{
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.modal.edit-purchases-orders');
    }
}
