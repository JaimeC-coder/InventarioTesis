<?php

namespace App\Livewire\Admin\Edit;

use App\Models\Measure as ModelsMeasure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Measure extends Component
{
    public ModelsMeasure $measure;

    public string $name;

    public string $abbreviation;

    public string $code;

    public string $category;

    public string $description_for_product;

    public function mount(ModelsMeasure $modelsMeasure): void
    {
        $this->measure = $modelsMeasure;
        $this->name = $modelsMeasure->name;
        $this->abbreviation = $modelsMeasure->abbreviation;
        $this->code = $modelsMeasure->code;
        $this->category = $modelsMeasure->category;
        $this->description_for_product = $modelsMeasure->description_for_product;
    }

    public function limpiar(): void
    {
        $this->reset(['name', 'abbreviation', 'code', 'category', 'description_for_product']);
        $this->name = $this->measure->name;
        $this->abbreviation = $this->measure->abbreviation;
        $this->code = $this->measure->code;
        $this->category = $this->measure->category;
        $this->description_for_product = $this->measure->description_for_product;
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
            $this->measure->update([
                'name' => $this->name,
                'abbreviation' => $this->abbreviation,
                'code' => $this->code,
                'category' => $this->category,
                'description_for_product' => $this->description_for_product,
            ]);
            DB::commit();
            session()->flash('message', 'Unidad actualizada exitosamente.');
            // Reset the form fields
            $this->limpiar();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Error al actualizar medida: ' . $exception->getMessage());
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Ocurrió un error al actualizar la medida.',
            ]);
        }
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.edit.measure');
    }
}
