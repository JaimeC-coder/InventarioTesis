<?php

namespace App\Livewire\Admin\Edit;

use App\Models\Category as ModelsCategory;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Category extends Component
{
    public ModelsCategory $category;

    public  $name = '', $description  = '';

    public  $codigo = 0;



    public function mount(ModelsCategory $modelsCategory): void
    {
        $this->category = $modelsCategory;
        $this->name = $modelsCategory->name;
        $this->description = $modelsCategory->description;
        $this->codigo = $modelsCategory->codigo;
    }

    public function limpiar(): void
    {
        $this->name = $this->category->name;
        $this->description = $this->category->description;
        $this->codigo = $this->category->codigo;
    }


    public function save(): void
    {

        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'codigo' => 'required|integer|unique:categories,codigo,' . $this->category->id,
        ]);

        DB::beginTransaction();
        try {
            $this->category->update([
                'name' => $this->name,
                'description' => $this->description,
                'codigo' => $this->codigo,
            ]);
            DB::commit();

            $this->dispatch('pg:eventRefresh-category-table-itbilq-table'); // refresca tabla PowerGrid
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error al actualizar categoría: ' . $e->getMessage());
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Ocurrió un error al actualizar la categoría.',
            ]);

        }
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.edit.category');
    }
}
