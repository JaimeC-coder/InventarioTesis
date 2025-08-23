<?php

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::post('suppliers', function (Request $request) {
    $supplier = Supplier::select('uuid', 'name')
        ->when($request->search, function ($query) use ($request): void {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('document_number', 'like', '%' . $request->search . '%');
        })
        ->when(
            $request->exists('selected'),
            fn($query) => $query->whereIn('id', $request->input('selected')),
            fn($query) => $query->limit(10)
        )
        ->get();
    Log::info('Suppliers fetched', ['count' => $supplier->count(), 'search' => $request->search, 'selected' => $request->input('selected'), 'all' => $supplier->toArray()]);
    // Aquí está el cambio clave: convierte la colección a un array antes de enviarla
    return response()->json($supplier);
})->name('admin.suppliers');
