<?php

namespace App\Livewire\Admin;

use App\Enum\DocumentEnum;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use App\Services\DocumentServices;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class CustomerCreate extends Component
{
    public $document_number = '';

    public $identity = '';

    public $name = '';

    public $email = '';

    public $phone = '';

    public $address = '';

    public $active = false;

    public $identities = [];

    public $type = '';

    public $types = [];

    //CustomerRequest
    public function mount(): void
    {
        $this->identities = collect(DocumentEnum::cases())->map(fn($mes): array => [
            'id' => $mes,
            'name' => $mes->label(),
        ])->toArray();
        $this->types = [
            ['id' => 'GENERAL', 'name' => 'GENERAL'],
            ['id' => 'A1', 'name' => 'A1'],
        ];
    }

    public function updatedIdentity(): void
    {
        $this->name = '';
        $this->active = in_array($this->identity, [DocumentEnum::DNI->value, DocumentEnum::RUC->value]);
    }

    public function generateDocumentNumber(): void
    {
        if ($this->identity === DocumentEnum::DNI->value) {
            $identity  = DocumentServices::getDataFromDNI($this->document_number);
            if ($identity['success'] === false) {
                $this->dispatch('swal', [
                    'icon' => 'error',
                    'title' => 'Error',
                    'text' => 'No se pudo obtener la información del documento.',
                ]);
                $this->active = false;
                $this->name = '';
                return;
            }

            $this->name = $identity['nombres'] . ' ' . $identity['apellidoPaterno'] . ' ' . $identity['apellidoMaterno'];
        } elseif ($this->identity === DocumentEnum::RUC->value) {
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
        $customerRequest = new CustomerRequest();
        $this->validate($customerRequest->rulesForAction('POST'), $customerRequest->messages());
        try {
            Customer::create([
                'document_number' => $this->document_number,
                'identity' => $this->identity,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'address' => $this->address,
                'type' => $this->type,
            ]);
            $this->dispatch('swal', [
                'title' => 'Exitoso',
                'text' => 'La creación del cliente fue exitosa.',
                'icon' => 'success',
            ]);

            return redirect()->route('admin.customers.index');
        } catch (\Exception $exception) {
            Log::error('Error al crear el cliente: ' . $exception->getMessage(), [
                'stack' => $exception->getTraceAsString(),
            ]);
            $this->dispatch('swal', [
                'title' => 'Error',
                'text' => 'Hubo un problema al crear el cliente.',
                'icon' => 'error',
            ]);
        } catch (\Throwable $exception) {
            Log::error('Error al crear el cliente: ' . $exception->getMessage(), [
                'stack' => $exception->getTraceAsString(),
            ]);
            $this->dispatch('swal', [
                'title' => 'Error',
                'text' => 'Hubo un problema al crear el cliente.',
                'icon' => 'error',
            ]);
        }
        return null;

    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {

        return view('livewire.admin.customer-create');
    }
}
