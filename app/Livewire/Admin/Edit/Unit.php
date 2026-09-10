<?php

namespace App\Livewire\Admin\Edit;

use App\Models\Unit as ModelsUnit;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Unit extends Component
{
    public ModelsUnit $unit;

    public string $name;

    public string $abbreviation;

    public string $code;

    public function mount(ModelsUnit $modelsUnit): void
    {
        $this->unit = $modelsUnit;
        $this->name = $modelsUnit->name;
        $this->abbreviation = $modelsUnit->abbreviation;
        $this->code = $modelsUnit->code;
    }

    public function limpiar(): void
    {
        $this->reset(['name', 'abbreviation', 'code']);
        $this->name = $this->unit->name;
        $this->abbreviation = $this->unit->abbreviation;
        $this->code = $this->unit->code;
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
            $this->unit->update([
                'name' => $this->name,
                'abbreviation' => $this->abbreviation,
                'code' => $this->code,
            ]);
            DB::commit();
            session()->flash('message', 'Unidad actualizada exitosamente.');
            // Reset the form fields
            $this->limpiar();
        } catch (\Exception $exception) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error al crear unidad: ' . $exception->getMessage());
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Ocurrió un error al actualizar la unidad.',
            ]);
        }
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.edit.unit');
    }
}
