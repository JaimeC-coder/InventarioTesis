<?php

namespace App\Livewire\Admin\Edit;

use App\Enum\DocumentEnum;
use App\Http\Requests\SupplierRequest;
use App\Models\Supplier as ModelsSupplier;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Enum;
use Livewire\Component;

class Supplier extends Component
{
    public ModelsSupplier $supplier;

    public $document_number = '';

    public $identity = '';

    public $name = '';

    public $email = '';

    public $phone = '';

    public $address = '';

    public $identities = [];

    public function mount(ModelsSupplier $modelsSupplier): void
    {
        $this->supplier = $modelsSupplier;
        $this->document_number = $modelsSupplier->document_number;
        $this->identity = $modelsSupplier->identity;
        $this->name = $modelsSupplier->name;
        $this->email = $modelsSupplier->email;
        $this->phone = $modelsSupplier->phone;
        $this->address = $modelsSupplier->address;
        $this->identities = [[
            'id' => DocumentEnum::RUC->value,
            'name' => DocumentEnum::RUC->label(),
        ]];
    }

    protected function rules(): array
    {
        return [
            'identity' => ['required', new Enum(DocumentEnum::class)],
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
        ];
    }

    public function limpiar(): void
    {
        $this->identity = $this->supplier->identity;
        $this->name = $this->supplier->name;
        $this->email = $this->supplier->email;
        $this->phone = $this->supplier->phone;
        $this->address = $this->supplier->address;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate($this->rules(), (new SupplierRequest())->messages());
        try {
            $this->supplier->update([
                'identity' => $this->identity,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'address' => $this->address,
            ]);
            $this->dispatch('swal', [
                'title' => 'Exitoso',
                'text' => 'La actualización del proveedor fue exitosa.',
                'icon' => 'success',
            ]);

            return redirect()->route('admin.suppliers.index');
        } catch (\Throwable $throwable) {
            Log::error('Error al actualizar el proveedor: ' . $throwable->getMessage(), [
                'stack' => $throwable->getTraceAsString(),
            ]);
            $this->dispatch('swal', [
                'title' => 'Error',
                'text' => 'Hubo un problema al actualizar el proveedor.',
                'icon' => 'error',
            ]);
        }

        return null;
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.edit.supplier');
    }
}
