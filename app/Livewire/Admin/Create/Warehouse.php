<?php

namespace App\Livewire\Admin\Create;

use App\Models\Warehouse as ModelsWarehouse;
use Livewire\Component;

class Warehouse extends Component
{
    public string $name;

    public string $location;

    public function limpiar(): void
    {
        $this->reset([
            'name',
            'location',
        ]);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);
        ModelsWarehouse::create([
            'name' => $this->name,
            'location' => $this->location,
        ]);
        session()->flash('message', 'Almacén creado exitosamente.');
        $this->limpiar();
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.create.warehouse');
    }
}
