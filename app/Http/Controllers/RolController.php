<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RolController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\View\View
    {
        $this->authorize('viewAny', Role::class);

        return view('admin.roles.index');
    }

    public function permissionsIndex(): \Illuminate\View\View
    {
        $this->authorize('admin.permissions.index');

        return view('admin.roles.indexPermissions');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\View\View
    {
        $this->authorize('create', Role::class);

        return view('admin.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): void
    {
        $this->authorize('create', Role::class);
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
    public function edit(Role $role): \Illuminate\View\View
    {
        $this->authorize('update', $role);

        return view('admin.roles.edit', ['role' => $role]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role): void
    {
        $this->authorize('update', $role);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): void
    {
        $this->authorize('delete', $role);
    }
}
