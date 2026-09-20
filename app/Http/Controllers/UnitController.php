<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        $this->authorize('viewAny', Unit::class);

        return view('admin.units.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        $this->authorize('create', Unit::class);

        return view('admin.units.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): void
    {
        $this->authorize('create', Unit::class);
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Unit $unit): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        $this->authorize('update', $unit);

        return view('admin.units.edit', [
            'unit' => $unit,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit): void
    {
        $this->authorize('update', $unit);
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit): void
    {
        $this->authorize('delete', $unit);
        //
    }
}
