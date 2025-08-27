<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\View\View
    {
        return view('admin.purchases.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\View\View
    {
        return view('admin.purchases.create');
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
