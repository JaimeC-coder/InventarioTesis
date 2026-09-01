<?php

namespace App\Livewire\Admin\Create;

use Livewire\Component;

class Unit extends Component
{
    public string $name, $abbreviation, $code;

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'abbreviation' => 'required|string|max:10',
            'code' => 'required|string|max:10',
        ]);

        \App\Models\Unit::create([
            'name' => $this->name,
            'abbreviation' => $this->abbreviation,
            'code' => $this->code,
        ]);

        session()->flash('message', 'Unidad creada exitosamente.');

        // Reset the form fields
        $this->reset(['name', 'abbreviation', 'code']);
    }


    public function render()
    {
        return view('livewire.admin.create.unit');
    }
}
