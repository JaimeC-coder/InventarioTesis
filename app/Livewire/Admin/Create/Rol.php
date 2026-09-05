<?php

namespace App\Livewire\Admin\Create;

use Livewire\Component;

class Rol extends Component
{
    public string $name;

    public array $selectedPermissions = [];

    public $allPermissions = [];

    public function mount(): void
    {
        $this->allPermissions = \Spatie\Permission\Models\Permission::Select('id', 'description')->get();
        $this->selectedPermissions = [];
    }

    public function limpiar(): void
    {
        $this->reset([
            'name',
            'selectedPermissions',
        ]);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'selectedPermissions' => 'array',
            'selectedPermissions.*' => 'exists:permissions,id',
        ]);
        $role = \Spatie\Permission\Models\Role::create(['name' => $this->name]);
        $role->syncPermissions(array_map('intval', $this->selectedPermissions));
        $this->dispatch('swal', [
            'title' => 'Exitoso',
            'text' => 'La creación del Rol fue exitosa.',
            'icon' => 'success',
        ]);
        $this->limpiar();

        return redirect()->route('admin.roles.index');
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.create.rol');
    }
}
