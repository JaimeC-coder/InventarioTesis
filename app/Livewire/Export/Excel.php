<?php

namespace App\Livewire\Export;

use Livewire\Component;

class Excel extends Component
{
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.export.excel');
    }
}
