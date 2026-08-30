<?php

namespace App\Livewire\Admin\Create;

use Livewire\Component;

class Rol extends Component
{
    public string $name;
    public array $permissions = [];
    public $allPermissions = [];



    public function mount()
    {
        $this->allPermissions = \Spatie\Permission\Models\Permission::Select('id', 'description')->get();
        $this->permissions = [];
    }



    public function save()
    {
        dd($this);
        $this->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = \Spatie\Permission\Models\Role::create(['name' => $this->name]);
        $role->syncPermissions($this->permissions);

        $this->dispatch('swal', [
            'title' => 'Exitoso',
            'text' => 'La creación del Rol fue exitosa.',
            'icon' => 'success',
        ]);

        $this->reset();

        return redirect()->route('admin.roles.index');
    }
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.create.rol');
    }
}
