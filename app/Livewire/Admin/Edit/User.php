<?php

namespace App\Livewire\Admin\Edit;

use Livewire\Component;

class User extends Component
{
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.edit.user');
    }
}
