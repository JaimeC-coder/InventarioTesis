<?php

namespace App\Livewire\Admin;

use App\Enum\DocumentEnum;
use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use App\Services\DocumentServices;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class SupplierCreate extends Component
{
    public $document_number = '';

    public $identity = '';

    public $name = '';

    public $email = '';

    public $phone = '';

    public $address = '';

    public $active = false;

    public $identities = [];



    public function mount(): void
    {

        $this->identities =[[
            'id' => DocumentEnum::RUC->value,
            'name' => DocumentEnum::RUC->label(),
        ]];
    }

    public function updatedIdentity(): void
    {
        $this->name = '';
        $this->active = $this->identity === DocumentEnum::RUC->value;

    }

    public function generateDocumentNumber(): void
    {
       if ($this->identity === DocumentEnum::RUC->value) {
            $identity  = DocumentServices::getDataFromRUC($this->document_number);
            if (isset($identity['success']) && $identity['success'] === false) {
                $this->dispatch('swal', [
                    'icon' => 'error',
                    'title' => 'Error',
                    'text' => 'No se pudo obtener la información del documento.',
                ]);
                $this->active = false;
                $this->name = '';
                return;
            }

            $this->name = $identity['nombreComercial'] ?? $identity['razonSocial'] ?? '';
        } else {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Tipo de documento no soportado.',
            ]);
            $this->active = false;
            $this->name = '';
            return;
        }
    }

    public function save()
    {
        try {
            $customerRequest = new SupplierRequest();
            $this->validate($customerRequest->rulesForAction('POST'), $customerRequest->messages());
            Supplier::create([
                'document_number' => $this->document_number,
                'identity' => $this->identity,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'address' => $this->address,

            ]);
            $this->dispatch('swal', [
                'title' => 'Exitoso',
                'text' => 'La creación del proveedor fue exitosa.',
                'icon' => 'success',
            ]);

            return redirect()->route('admin.suppliers.index');
        } catch (\Exception $exception) {
            Log::error('Error al crear el proveedor: ' . $exception->getMessage(), [
                'stack' => $exception->getTraceAsString(),
            ]);
            $this->dispatch('swal', [
                'title' => 'Error',
                'text' => 'Hubo un problema al crear el proveedor.',
                'icon' => 'error',
            ]);
        }

        return null;
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.supplier-create');
    }
}
