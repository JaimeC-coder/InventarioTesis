<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use App\Traits\HandlesSwalMessagesTrait;

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
        $identities = \App\Models\Identity::select('name', 'uuid')->get(); // Assuming you have an Identity model
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
        } catch (\Exception $exception) {
            $this->successSwal('Hubo un problema al crear el cliente.', type: 'session');
        }

        return redirect()->route('admin.customers.index');
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
        $identities = \App\Models\Identity::select('name', 'uuid')->get(); // Assuming you have an Identity model
        return view('admin.customers.edit', ['customer' => $customer, 'identities' => $identities]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerRequest $customerRequest, Customer $customer): \Illuminate\Http\RedirectResponse
    {
        try {
            $customer->update($customerRequest->validated());
            $this->successSwal('La actualización del cliente fue exitosa.', type: 'session');

            return redirect()->route('admin.customers.index');
        } catch (\Exception $exception) {
            $this->successSwal('Hubo un problema al actualizar el cliente.', type: 'session');
            return redirect()->route('admin.customers.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer): \Illuminate\Http\RedirectResponse
    {
        if ($customer->sales()->exists() || $customer->quotes()->exists()) {
            $this->successSwal('No se puede eliminar el cliente porque tiene ventas o cotizaciones asociadas.', type: 'session');
            return redirect()->route('admin.customers.index');
        }

        $customer->delete();
        $this->successSwal('El cliente fue eliminado exitosamente.', type: 'session');

        return redirect()->route('admin.customers.index');
    }
}
