<?php

namespace App\Livewire\Admin\Create;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Measure extends Component
{
    public string $name;

    public string $abbreviation;

    public string $code;

    public string $category;

    public string $description_for_product;

    public function limpiar(): void
    {
        $this->reset(['name', 'abbreviation', 'code', 'category', 'description_for_product']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'abbreviation' => 'required|string|max:10',
            'code' => 'required|string|max:10',
            'category' => 'required|string|in:LIQUIDO,PESO',
            'description_for_product' => 'nullable|string|max:255',
        ]);
        DB::beginTransaction();
        try {
            \App\Models\Measure::create([
                'name' => $this->name,
                'abbreviation' => $this->abbreviation,
                'code' => $this->code,
                'category' => $this->category,
                'description_for_product' => $this->description_for_product,
            ]);
            DB::commit();
            session()->flash('message', 'Unidad creada exitosamente.');
            // Reset the form fields
            $this->limpiar();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear medida: ' . $e->getMessage());
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Ocurrió un error al crear la medida.',
            ]);
        }
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.create.measure');
    }
}
