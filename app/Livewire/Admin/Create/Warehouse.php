<?php

namespace App\Livewire\Admin\Create;

use App\Models\Warehouse as ModelsWarehouse;
use Illuminate\Support\Facades\DB;
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
        DB::beginTransaction();
        try {
            ModelsWarehouse::create([
                'name' => $this->name,
                'location' => $this->location,
            ]);
            DB::commit();
            session()->flash('message', 'Almacén creado exitosamente.');
            $this->limpiar();
        } catch (\Throwable $throwable) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error al crear almacén: ' . $throwable->getMessage());
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Ocurrió un error al crear el almacén.',
            ]);
        }
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.create.warehouse');
    }
}
