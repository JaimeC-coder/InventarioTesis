<?php

namespace App\Http\Controllers;

use App\Enum\DocumentEnum;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use App\Traits\HandlesSwalMessagesTrait;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    use HandlesSwalMessagesTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\View\View
    {
        return view('admin.customers.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\View\View
    {
        $identities = collect(DocumentEnum::cases())->map(fn($mes): array => [
            'id' => $mes->value,
            'name' => $mes->label(),
        ])->toArray();
        return view('admin.customers.create', ['identities' => $identities]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerRequest $customerRequest): \Illuminate\Http\RedirectResponse
    {
        try {
            Customer::create($customerRequest->validated());
            $this->successSwal('La creación del cliente fue exitosa.', type: 'session');
            return redirect()->route('admin.customers.index');
        } catch (\Exception $exception) {
            Log::info('Error al crear cliente: ' . $exception->getMessage());
            $this->errorSwal('Hubo un problema al crear el cliente.', type: 'session');
            return redirect()->back();
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer): void
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer): \Illuminate\View\View
    {
        return view('admin.customers.edit', ['customer' => $customer]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerRequest $customerRequest, Customer $customer)
    {
        try {
            $customer->update($customerRequest->validated());
            $this->successSwal('La actualización del cliente fue exitosa.', type: 'session');
            return redirect()->route('admin.customers.index');
        } catch (\Exception $exception) {
            Log::info('Error al actualizar cliente: ' . $exception->getMessage());
            $this->errorSwal('Hubo un problema al actualizar el cliente.', type: 'session');
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer): \Illuminate\Http\RedirectResponse
    {
        if ($customer->sales()->exists() || $customer->quotes()->exists()) {
            $this->warningSwal('No se puede eliminar el cliente porque tiene ventas o cotizaciones asociadas.', type: 'session');
            return redirect()->route('admin.customers.index');
        }

        $customer->delete();
        $this->successSwal('El cliente fue eliminado exitosamente.', type: 'session');

        return redirect()->route('admin.customers.index');
    }
}
