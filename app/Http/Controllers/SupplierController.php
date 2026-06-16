<?php

namespace App\Http\Controllers;

use App\Enum\DocumentEnum;
use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;

class SupplierController extends Controller
{
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
    public function create()
    {
        $identities = collect(DocumentEnum::cases())->map(fn($mes): array => [
            'id' => $mes->label(),
            'name' => $mes->label(),
        ])->toArray();

        return view('admin.suppliers.create', ['identities' => $identities]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SupplierRequest $supplierRequest): \Illuminate\Http\RedirectResponse
    {
        try {
            Supplier::create($supplierRequest->validated());
            session()->flash('swal', [
                'title' => 'Exitoso',
                'text' => 'La creación del proveedor fue exitosa.',
                'icon' => 'success',
            ]);
        } catch (\Exception $exception) {
            session()->flash('swal', [
                'title' => 'Error',
                'text' => 'Hubo un problema al crear el proveedor.',
                'icon' => 'error',
            ]);
        }

        return redirect()->route('admin.suppliers.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier): void {}

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
            session()->flash('swal', [
                'title' => 'Exitoso',
                'text' => 'La actualización del proveedor fue exitosa.',
                'icon' => 'success',
            ]);
        } catch (\Exception $exception) {
            session()->flash('swal', [
                'title' => 'Error',
                'text' => 'Hubo un problema al actualizar el proveedor.',
                'icon' => 'error',
            ]);
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
                session()->flash('swal', [
                    'title' => 'Error',
                    'text' => 'No se puede eliminar el proveedor porque tiene ventas o cotizaciones asociadas.',
                    'icon' => 'error',
                ]);
                return redirect()->route('admin.suppliers.index');
            }

            $supplier->delete();
            session()->flash('swal', [
                'title' => 'Exitoso',
                'text' => 'El proveedor fue eliminado exitosamente.',
                'icon' => 'success',
            ]);

            return redirect()->route('admin.suppliers.index');
        } catch (\Exception $exception) {
            session()->flash('swal', [
                'title' => 'Error',
                'text' => 'Hubo un problema al eliminar el proveedor.',
                'icon' => 'error',
            ]);
            return redirect()->route('admin.suppliers.index');
        }
    }
}
