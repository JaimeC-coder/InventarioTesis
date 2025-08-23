<?php

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
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
    return response()->json($supplier);
})->name('admin.suppliers');

Route::post('products', function (Request $request) {
    $product = Product::select('uuid', 'name')
        ->when($request->search, function ($query) use ($request): void {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('sku', 'like', '%' . $request->search . '%');
        })
        ->when(
            $request->exists('selected'),
            fn($query) => $query->whereIn('id', $request->input('selected')),
            fn($query) => $query->limit(10)
        )
        ->get();
    return response()->json($product);
})->name('admin.products');
