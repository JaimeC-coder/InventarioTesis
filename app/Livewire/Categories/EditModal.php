<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Livewire\Component;

class EditModal extends Component
{
    public $categoryId;

    public $name;

    public $description;

    public $showModal = false;

    #[\Livewire\Attributes\On('editCategory')]
    public function loadCategory($categoryId): void
    {
        $category = Category::where('uuid', $categoryId)->first();
       
        if ($category) {
            $this->categoryId = $category->id;
            $this->name = $category->name;
            $this->description = $category->description;
            $this->showModal = true;
        }
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $category = Category::find($this->categoryId);
        if ($category) {
            $category->update([
                'name' => $this->name,
                'description' => $this->description,
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
