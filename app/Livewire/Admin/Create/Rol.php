<?php

namespace App\Livewire\Admin\Create;

use Illuminate\Support\Facades\DB;
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

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'selectedPermissions' => 'array',
            'selectedPermissions.*' => 'exists:permissions,id',
        ]);
        DB::beginTransaction();
        try {
            $role = \Spatie\Permission\Models\Role::create(['name' => $this->name]);
            $role->syncPermissions($this->selectedPermissions);
            DB::commit();
            session()->flash('message', 'Rol creado exitosamente.');
            $this->limpiar();
        } catch (\Exception $exception) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error al crear rol: ' . $exception->getMessage());
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Ocurrió un error al crear el rol.',
            ]);
        }
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.create.rol');
    }
}
