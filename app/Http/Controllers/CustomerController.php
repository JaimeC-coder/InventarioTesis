<?php

namespace App\Http\Controllers;

use App\Enum\DocumentEnum;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;

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
        $identities = collect(DocumentEnum::cases())->map(fn($mes): array => [
            'id' => $mes->value,
            'name' => $mes->label(),
        ])->toArray();
        return view('admin.customers.create', ['identities' => $identities]);
    }

    public function show(Customer $customer): void
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer): \Illuminate\View\View
    {
        $identities = collect(DocumentEnum::cases())->map(fn($mes): array => [
            'uuid' => $mes->value,
            'name' => $mes->label(),
        ])->toArray(); // Assuming you have an Identity model
        $type = [
            ['uuid' => 'GENERAL', 'name' => 'GENERAL'],
            ['uuid' => 'A1', 'name' => 'A1'],
        ];

        return view('admin.customers.edit', ['customer' => $customer, 'identities' => $identities, 'types' => $type]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerRequest $customerRequest, Customer $customer)
    {
        try {
            $customer->update($customerRequest->validated());
            session()->flash('swal', [
                'title' => 'Exitoso',
                'text' => 'La actualización del cliente fue exitosa.',
                'icon' => 'success',
            ]);
            return redirect()->route('admin.customers.index');
        } catch (\Exception $exception) {
            Log::info('Error al actualizar cliente: ' . $exception->getMessage());
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
