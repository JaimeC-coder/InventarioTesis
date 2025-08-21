<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

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
    public function create(): \Illuminate\View\View
    {
        $identities = \App\Models\Identity::all(); // Assuming you have an Identity model
        return view('admin.suppliers.create', compact('identities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        Supplier::create($request->validate([
            'identity_id' => 'required|exists:identities,id',
            'document_number' => 'required|numeric|unique:suppliers,document_number',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
        ]));

        session()->flash('swal', [
            'title' => 'Exitoso',
            'text' => 'La creación del proveedor fue exitosa.',
            'icon' => 'success',
        ]);

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
        $identities = \App\Models\Identity::all(); // Assuming you have an Identity model
        return view('admin.suppliers.edit', compact('supplier', 'identities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier): \Illuminate\Http\RedirectResponse
    {
        $supplier->update($request->validate([
            'identity_id' => 'required|exists:identities,id',
            'document_number' => 'required|numeric|unique:suppliers,document_number,' . $supplier->id,
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
        ]));

        session()->flash('swal', [
            'title' => 'Exitoso',
            'text' => 'La actualización del proveedor fue exitosa.',
            'icon' => 'success',
        ]);

        return redirect()->route('admin.suppliers.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier): \Illuminate\Http\RedirectResponse
    {
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
    }
}
