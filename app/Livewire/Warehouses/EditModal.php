<?php

namespace App\Livewire\Warehouses;

use App\Models\Warehouse;
use Livewire\Component;

class EditModal extends Component
{
    public $warehouseId;

    public $name;

    public $location;

    public $showModal = false;

    #[\Livewire\Attributes\On('editWarehouse')]
    public function loadWarehouse($warehouseId): void
    {
        $warehouse = Warehouse::where('uuid', $warehouseId)->first();
        if ($warehouse) {
            $this->warehouseId = $warehouse->id;
            $this->name = $warehouse->name;
            $this->location = $warehouse->location;
            $this->showModal = true;
        }
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string',
        ]);
        $warehouse = Warehouse::find($this->warehouseId);
        if ($warehouse) {
            $warehouse->update([
                'name' => $this->name,
                'location' => $this->location,
            ]);
            $this->showModal = false;
            $this->dispatch('pg:eventRefresh-warehouse-table-itbilq-table'); // refresca tabla PowerGrid
        }
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.warehouses.edit-modal');
    }
}
