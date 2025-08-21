<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\View\View
    {
        return view('admin.warehouses.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\View\View
    {
        return view('admin.warehouses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ]);

        Warehouse::create($validated);

         session()->flash('swal', [
            'title' => 'Exitoso',
            'text' => 'La creación del almacén fue exitosa.',
            'icon' => 'success',
        ]);


        return redirect()->route('admin.warehouses.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Warehouse $warehouse): void
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Warehouse $warehouse): \Illuminate\View\View
    {

        return view('admin.warehouses.edit', compact('warehouse'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Warehouse $warehouse): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ]);

        $warehouse->update($validated);

        session()->flash('swal', [
            'title' => 'Exitoso',
            'text' => 'La actualización del almacén fue exitosa.',
            'icon' => 'success',
        ]);

        return redirect()->route('admin.warehouses.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Warehouse $warehouse): \Illuminate\Http\RedirectResponse
    {
        if ( $warehouse->inventories()->count() > 0 ) {
            session()->flash('swal', [
                'title' => 'Error',
                'text' => 'No se puede eliminar el almacén porque tiene productos asociados.',
                'icon' => 'error',
            ]);

            return redirect()->route('admin.warehouses.index');
        }

        $warehouse->delete();

        session()->flash('swal', [
            'title' => 'Exitoso',
            'text' => 'La eliminación del almacén fue exitosa.',
            'icon' => 'success',
        ]);

        return redirect()->route('admin.warehouses.index');
    }
}
