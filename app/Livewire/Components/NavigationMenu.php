<?php

namespace App\Livewire\Components;

use Livewire\Component;

class NavigationMenu extends Component
{
    public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $links = \App\config\array_nav_use::items();
        return view('livewire.navigation_menu', ['links' => $links]);
    }
}
