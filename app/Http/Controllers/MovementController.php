<?php

namespace App\Http\Controllers;

use App\Models\Movement;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class MovementController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\View\View
    {
        $this->authorize('viewAny', Movement::class);

        return view('admin.movements.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\View\View
    {
        $this->authorize('create', Movement::class);

        return view('admin.movements.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): void
    {
        $this->authorize('create', Movement::class);
    }

    /**
     * Display the specified resource.
     */
    public function show(Movement $movement): void
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Movement $movement): void
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Movement $movement): void
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movement $movement): void
    {
    }
}
