<?php

namespace App\Livewire\Admin\Edit;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Rol extends Component
{
    public Role $role;

    public array $selected = [];

    public string $name;

    public array $permission;

    public array $selectedPermissions = [];

    public $allPermissions = [];

    public function mount(Role $modelsRole): void
    {
        $this->role = $modelsRole;
        $this->name = $modelsRole->name;
        $this->selectedPermissions = $modelsRole->permissions->pluck('id')->toArray();
        $this->allPermissions = \Spatie\Permission\Models\Permission::Select('id', 'description')->get();
    }

    public function limpiar(): void
    {
        $this->reset([
            'name',
            'selectedPermissions',
        ]);
        $this->name = $this->role->name;
        $this->selectedPermissions = $this->role->permissions->pluck('id')->toArray();
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $this->role->id,
            'selectedPermissions' => 'array',
            'selectedPermissions.*' => 'exists:permissions,id',
        ]);
        DB::beginTransaction();
        try {
            $permissions = array_map('intval', $this->selectedPermissions);
            $this->role->update([
                'name' => $this->name,
            ]);
            $this->role->syncPermissions($permissions);
            DB::commit();
            session()->flash('message', 'Rol actualizado exitosamente.');
            return redirect()->route('admin.roles.index');
        } catch (\Exception $exception) {
            Log::error('Error al actualizar rol: ' . $exception->getMessage());
            DB::rollBack();
            session()->flash('error', 'Error al actualizar el rol.');
        }
        return null;
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.edit.rol');
    }
}
