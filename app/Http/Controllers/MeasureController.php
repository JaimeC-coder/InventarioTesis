<?php

namespace App\Http\Controllers;

use App\Models\Measure;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class MeasureController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        $this->authorize('viewAny', Measure::class);

        return view('admin.measures.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        $this->authorize('create', Measure::class);

        return view('admin.measures.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): void
    {
        $this->authorize('create', Measure::class);
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
    public function edit(Measure $measure): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        $this->authorize('update', $measure);

        return view('admin.measures.edit', ['measure' => $measure]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Measure $measure): void
    {
        $this->authorize('update', $measure);
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Measure $measure): void
    {
        $this->authorize('delete', $measure);
        //
    }
}
