<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\View\View
    {
        return view('admin.transfers.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\View\View
    {
        return view('admin.transfers.create');
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
    public function show(Transfer $transfer): void
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transfer $transfer): void
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transfer $transfer): void
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transfer $transfer): void
    {
    }
}
