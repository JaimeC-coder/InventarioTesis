<?php

namespace App\Http\Controllers;

use App\Models\unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('admin.units.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('admin.units.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): void
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(unit $unit): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(unit $unit): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, unit $unit): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(unit $unit): void
    {
        //
    }
}
