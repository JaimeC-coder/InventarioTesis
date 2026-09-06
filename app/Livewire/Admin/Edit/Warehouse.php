<?php

namespace App\Livewire\Admin\Edit;

use App\Models\Warehouse as ModelsWarehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Warehouse extends Component
{

  public ModelsWarehouse $warehouse;
    public $warehouseId;

    public $name;

    public $location;

    public $showModal = false;

    public function mount(ModelsWarehouse $modelsWarehouse): void
    {
        $this->warehouse = $modelsWarehouse;
        $this->warehouseId = $modelsWarehouse->id;
        $this->name = $modelsWarehouse->name;
        $this->location = $modelsWarehouse->location;
        $this->showModal = true;
    }

    public function limpiar(): void
    {
        $this->name = $this->warehouse->name;
        $this->location = $this->warehouse->location;
    }


    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string',
        ]);
        DB::beginTransaction();
        try {
            $warehouse = ModelsWarehouse::find($this->warehouseId);
            $warehouse->update([
                'name' => $this->name,
                'location' => $this->location,
            ]);
            $this->showModal = false;
            DB::commit();
            $this->dispatch('swal', [
                'title' => 'Exitoso',
                'text' => 'La actualización del almacén fue exitosa.',
                'icon' => 'success',
            ]);
            return redirect()->route('admin.warehouses.index');
        } catch (\Throwable $throwable) {
            DB::rollBack();
            Log::error('Error al actualizar el almacén: ' . $throwable->getMessage(), [
                'stack' => $throwable->getTraceAsString(),
            ]);
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Ocurrió un error al actualizar el almacén.',
            ]);
        }
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.edit.warehouse');
    }
}
