<?php

namespace App\Livewire\Admin\Create;

use Illuminate\Support\Facades\DB;
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
            'code',
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
        DB::beginTransaction();
        try {
            \App\Models\Unit::create([
                'name' => $this->name,
                'abbreviation' => $this->abbreviation,
                'code' => $this->code,
            ]);
            DB::commit();
            session()->flash('message', 'Unidad creada exitosamente.');
            // Reset the form fields
            $this->limpiar();
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error al crear unidad: ' . $e->getMessage());
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Ocurrió un error al crear la unidad.',
            ]);
        }
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.create.unit');
    }
}
