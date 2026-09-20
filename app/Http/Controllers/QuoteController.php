<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\View\View
    {
        $this->authorize('viewAny', Quote::class);

        return view('admin.quotes.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\View\View
    {
        $this->authorize('create', Quote::class);

        return view('admin.quotes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): void
    {
        $this->authorize('create', Quote::class);
    }

    /**
     * Display the specified resource.
     */
    public function show(Quote $quote): void
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quote $quote): void
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quote $quote): void
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quote $quote): void
    {
    }
}
