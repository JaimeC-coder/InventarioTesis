<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use App\Traits\HandlesSwalMessagesTrait;

class SupplierController extends Controller
{
    use HandlesSwalMessagesTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\View\View
    {
        return view('admin.suppliers.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\View\View
    {
        $identities = \App\Models\Identity::select('name', 'uuid')->get(); // Assuming you have an Identity model
        return view('admin.suppliers.create', ['identities' => $identities]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SupplierRequest $supplierRequest): \Illuminate\Http\RedirectResponse
    {
        try {
            Supplier::create($supplierRequest->validated());
            $this->successSwal('La creación del proveedor fue exitosa.', type: 'session');
        } catch (\Exception $exception) {
            $this->successSwal('Hubo un problema al crear el proveedor.', type: 'session');
        }

        return redirect()->route('admin.suppliers.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier): void
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier): \Illuminate\View\View
    {
        $identities = \App\Models\Identity::select('name', 'uuid')->get(); // Assuming you have an Identity model
        return view('admin.suppliers.edit', ['supplier' => $supplier, 'identities' => $identities]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SupplierRequest $supplierRequest, Supplier $supplier): \Illuminate\Http\RedirectResponse
    {
        try {
            $supplier->update($supplierRequest->validated());
            $this->successSwal('La actualización del proveedor fue exitosa.', type: 'session');
        } catch (\Exception $exception) {
            $this->successSwal('Hubo un problema al actualizar el proveedor.', type: 'session');
        }

        return redirect()->route('admin.suppliers.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier): \Illuminate\Http\RedirectResponse
    {
        try {
            if ($supplier->sales()->exists() || $supplier->quotes()->exists()) {
                $this->successSwal('No se puede eliminar el proveedor porque tiene ventas o cotizaciones asociadas.', type: 'session');
                return redirect()->route('admin.suppliers.index');
            }

            $supplier->delete();
            $this->successSwal('El proveedor fue eliminado exitosamente.', type: 'session');

            return redirect()->route('admin.suppliers.index');
        } catch (\Exception $exception) {
            $this->successSwal('Hubo un problema al eliminar el proveedor.', type: 'session');
            return redirect()->route('admin.suppliers.index');
        }
    }
}
