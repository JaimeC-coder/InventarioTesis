<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\View\View
    {
        $this->authorize('viewAny', User::class);

        return view('admin.users.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\View\View
    {
        $this->authorize('create', User::class);

        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): void
    {
        $this->authorize('create', User::class);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): void
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): \Illuminate\View\View
    {
        $this->authorize('update', $user);

        return view('admin.users.edit', ['user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user): void
    {
        $this->authorize('update', $user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): void
    {
        $this->authorize('delete', $user);
    }
}
