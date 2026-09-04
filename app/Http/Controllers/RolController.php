<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\View\View
    {
        return view('admin.roles.index');
    }

    public function permissionsIndex(): \Illuminate\View\View
    {
        return view('admin.roles.indexPermissions');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\View\View
    {
        return view('admin.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): void
    {
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role): void
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role): void
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role): void
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): void
    {
    }
}
