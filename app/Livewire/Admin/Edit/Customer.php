<?php

namespace App\Livewire\Admin\Edit;

use App\Enum\DocumentEnum;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer as ModelsCustomer;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Enum;
use Livewire\Component;

class Customer extends Component
{
    public ModelsCustomer $customer;

    public $document_number = '';

    public $identity = '';

    public $name = '';

    public $email = '';

    public $phone = '';

    public $address = '';

    public $identities = [];

    public $type = '';

    public $types = [];

    public function mount(ModelsCustomer $customer): void
    {
        $this->customer = $customer;
        $this->document_number = $customer->document_number;
        $this->identity = $customer->identity;
        $this->name = $customer->name;
        $this->email = $customer->email;
        $this->phone = $customer->phone;
        $this->address = $customer->address;
        $this->type = $customer->type;
        $this->identities = collect(DocumentEnum::cases())->map(fn($mes): array => [
            'id' => $mes,
            'name' => $mes->label(),
        ])->toArray();
        $this->types = [
            ['id' => 'GENERAL', 'name' => 'GENERAL'],
            ['id' => 'A1', 'name' => 'A1'],
        ];
    }

    protected function rules(): array
    {
        return [
            'identity' => ['required', new Enum(DocumentEnum::class)],
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20|regex:/^[0-9\-\(\)\s]+$/|min:7',
            'email' => 'required|email|max:255',
            'type' => 'required|in:GENERAL,A1',
        ];
    }

    public function limpiar(): void
    {
        $this->identity = $this->customer->identity;
        $this->name = $this->customer->name;
        $this->email = $this->customer->email;
        $this->phone = $this->customer->phone;
        $this->address = $this->customer->address;
        $this->type = $this->customer->type;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate($this->rules(), (new CustomerRequest())->messages());
        try {
            $this->customer->update([
                'identity' => $this->identity,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'address' => $this->address,
                'type' => $this->type,
            ]);
            $this->dispatch('swal', [
                'title' => 'Exitoso',
                'text' => 'La actualización del cliente fue exitosa.',
                'icon' => 'success',
            ]);

            return redirect()->route('admin.customers.index');
        } catch (\Throwable $exception) {
            Log::error('Error al actualizar el cliente: ' . $exception->getMessage(), [
                'stack' => $exception->getTraceAsString(),
            ]);
            $this->dispatch('swal', [
                'title' => 'Error',
                'text' => 'Hubo un problema al actualizar el cliente.',
                'icon' => 'error',
            ]);
        }

        return null;
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.edit.customer');
    }
}
