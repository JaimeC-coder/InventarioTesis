<?php

namespace App\Http\Controllers;

use App\Models\Measure;
use Illuminate\Http\Request;

class MeasureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('admin.measures.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('admin.measures.create');
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
    public function show(Measure $measure): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Measure $measure): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Measure $measure): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Measure $measure): void
    {
        //
    }
}
