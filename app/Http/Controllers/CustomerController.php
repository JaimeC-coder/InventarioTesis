<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
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
            session()->flash('swal', [
                'title' => 'Exitoso',
                'text' => 'La creación del cliente fue exitosa.',
                'icon' => 'success',
            ]);
        } catch (\Exception $exception) {
            session()->flash('swal', [
                'title' => 'Error',
                'text' => 'Hubo un problema al crear el cliente.',
                'icon' => 'error',
            ]);
        }

        return redirect()->route('admin.customers.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer): void {}

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
    public function update(CustomerRequest $request, Customer $customer): \Illuminate\Http\RedirectResponse
    {
        try {
            $customer->update($request->validated());
            session()->flash('swal', [
                'title' => 'Exitoso',
                'text' => 'La actualización del cliente fue exitosa.',
                'icon' => 'success',
            ]);

            return redirect()->route('admin.customers.index');
        } catch (\Exception $exception) {
            session()->flash('swal', [
                'title' => 'Error',
                'text' => 'Hubo un problema al actualizar el cliente.',
                'icon' => 'error',
            ]);
            return redirect()->route('admin.customers.index');
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer): \Illuminate\Http\RedirectResponse
    {
        if ($customer->sales()->exists() || $customer->quotes()->exists()) {
            session()->flash('swal', [
                'title' => 'Error',
                'text' => 'No se puede eliminar el cliente porque tiene ventas o cotizaciones asociadas.',
                'icon' => 'error',
            ]);
            return redirect()->route('admin.customers.index');
        }

        $customer->delete();
        session()->flash('swal', [
            'title' => 'Exitoso',
            'text' => 'El cliente fue eliminado exitosamente.',
            'icon' => 'success',
        ]);

        return redirect()->route('admin.customers.index');
    }
}
