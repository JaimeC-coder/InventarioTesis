<?php

namespace App\Livewire\components;

use Livewire\Component;

class NavigationMenu extends Component
{
    public function render()
    {
        $links = \App\config\array_nav_use::items();
        return view('livewire.navigation_menu', ['links' => $links]);
    }
}
