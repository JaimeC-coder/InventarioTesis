<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Traits\HandlesSwalMessagesTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PurchaseController extends Controller
{
    use AuthorizesRequests;

    use HandlesSwalMessagesTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\View\View
    {
        $this->authorize('viewAny', Purchase::class);

        return view('admin.purchases.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\View\View
    {
        $this->authorize('create', Purchase::class);

        return view('admin.purchases.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): void
    {
        $this->authorize('create', Purchase::class);
    }

    public function createFromReport(string $token): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        $this->authorize('create', Purchase::class);
        if (! Cache::has('low-stock-report:'.$token)) {
            $this->warningSwal(
                'Otro administrador ya hizo el pedido mediante correo.',
                'Pedido ya realizado',
                'session'
            );

            return redirect()->route('admin.dashboard');
        }

        return view('admin.purchases.create', ['token' => $token]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Purchase $purchase): void
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Purchase $purchase): void
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Purchase $purchase): void
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $purchase): void
    {
    }
}
