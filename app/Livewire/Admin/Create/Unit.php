<?php

namespace App\Livewire\Admin\Create;

use Livewire\Component;

class Unit extends Component
{
    public string $name;

    public string $abbreviation;

    public string $code;


    public function limpiar(): void
    {
        $this->reset([
            'name',
            'abbreviation',
            'code'
        ]);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function save(): void
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
        $this->limpiar();

    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.create.unit');
    }
}
