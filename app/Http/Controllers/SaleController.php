<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\View\View
    {
        return view('admin.sales.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\View\View
    {
        return view('admin.sales.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): void {}

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale): void {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale): void {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale): void {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale): void {}
}
