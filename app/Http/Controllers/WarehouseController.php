<?php

namespace App\Http\Controllers;

use App\Http\Requests\WarehouseRequest;
use App\Models\Warehouse;
use App\Traits\HandlesSwalMessagesTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;

class WarehouseController extends Controller
{
    use AuthorizesRequests;

    use HandlesSwalMessagesTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\View\View
    {
        $this->authorize('viewAny', Warehouse::class);

        return view('admin.warehouses.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\View\View
    {
        $this->authorize('create', Warehouse::class);

        return view('admin.warehouses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WarehouseRequest $warehouseRequest): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('create', Warehouse::class);
        try {
            $validated = $warehouseRequest->validated();
            Warehouse::create($validated);
            $this->successSwal('La creación del almacén fue exitosa.', type: 'session');

            return redirect()->route('admin.warehouses.index');
        } catch (\Exception $exception) {
            Log::info('Error al crear almacén: '.$exception->getMessage());
            $this->errorSwal('Hubo un problema al crear el almacén.', type: 'session');

            return redirect()->route('admin.warehouses.index');
        }
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
        $this->authorize('update', $warehouse);

        return view('admin.warehouses.edit', ['warehouse' => $warehouse]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WarehouseRequest $warehouseRequest, Warehouse $warehouse): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('update', $warehouse);
        try {
            $validated = $warehouseRequest->validated();
            $warehouse->update($validated);
            $this->successSwal('La actualización del almacén fue exitosa.', type: 'session');

            return redirect()->route('admin.warehouses.index');
        } catch (\Exception $exception) {
            Log::info('Error al actualizar almacén: '.$exception->getMessage());
            $this->errorSwal('Hubo un problema al actualizar el almacén.', type: 'session');

            return redirect()->route('admin.warehouses.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Warehouse $warehouse): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('delete', $warehouse);
        if ($warehouse->inventories()->count() > 0) {
            $this->warningSwal('No se puede eliminar el almacén porque tiene productos asociados.', type: 'session');

            return redirect()->route('admin.warehouses.index');
        }

        $warehouse->delete();
        $this->successSwal('La eliminación del almacén fue exitosa.', type: 'session');

        return redirect()->route('admin.warehouses.index');
    }
}
