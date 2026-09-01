<?php

namespace App\Livewire\Admin\Create;

use Livewire\Component;

class Measure extends Component
{
    public string $name,$abbreviation,$code,$category,$description_for_product;


    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'abbreviation' => 'required|string|max:10',
            'code' => 'required|string|max:10',
            'category' => 'required|string|in:LIQUIDO,PESO',
            'description_for_product' => 'nullable|string|max:255',
        ]);

        \App\Models\Measure::create([
            'name' => $this->name,
            'abbreviation' => $this->abbreviation,
            'code' => $this->code,
            'category' => $this->category,
            'description_for_product' => $this->description_for_product,
        ]);

        session()->flash('message', 'Unidad creada exitosamente.');

        // Reset the form fields
        $this->reset(['name', 'abbreviation', 'code', 'category', 'description_for_product']);
    }
    public function render()
    {
        return view('livewire.admin.create.measure');
    }
}
