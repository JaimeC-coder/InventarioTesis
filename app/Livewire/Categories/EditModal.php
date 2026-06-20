<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Livewire\Component;

class EditModal extends Component
{
    public $categoryId;

    public $name;

    public $description;

    public $codigo;

    public $showModal = false;

    #[\Livewire\Attributes\On('editCategory')]
    public function loadCategory($categoryId): void
    {
        $category = Category::where('uuid', $categoryId)->first();
        if ($category) {
            $this->categoryId = $category->uuid;
            $this->name = $category->name;
            $this->description = $category->description;
            $this->codigo = $category->codigo;
            $this->showModal = true;
        }
    }

    public function save(): void
    {
        $category = Category::where('uuid', $this->categoryId)->first();
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'codigo' => 'required|integer|unique:categories,codigo,' . $category->id,
        ]);
        if ($category) {
            $category->update([
                'name' => $this->name,
                'description' => $this->description,
                'codigo' => $this->codigo,
            ]);
            $this->showModal = false;
            $this->dispatch('pg:eventRefresh-category-table-oc8dnv-table'); // refresca tabla PowerGrid
        }
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.categories.edit-modal');
    }
}
