<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // This method can be used to return a view with the product table
        // For example, you can return a Livewire component that displays the products
        return view('admin.products.index'); // Assuming you have a view for listing products
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // This method can be used to return a view with a form for creating a new product
        $categories = \App\Models\Category::all(); // Fetch all categories if needed
        return view('admin.products.create', ['categories' => $categories]); // Assuming you have a view for creating products
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate and create the product
        Product::create($request->validate([
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:1',
            'category_id' => 'required|exists:categories,id',
        ]));
        session()->flash('swal', [
            'title' => 'Exitoso',
            'text' => 'El producto se ha creado correctamente.',
            'icon' => 'success',
        ]);

        return redirect()->route('admin.products.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): void
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): View
    {
        $categories = \App\Models\Category::all(); // Fetch all categories if needed
        return view('admin.products.edit', ['product' => $product, 'categories' => $categories]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        // Validate and update the product
        $product->update($request->validate([
            'name' => 'required|string|max:255|unique:products,name,' . $product->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:1',
            'category_id' => 'required|exists:categories,id',
        ]));
        session()->flash('swal', [
            'title' => 'Exitoso',
            'text' => 'El producto se ha actualizado correctamente.',
            'icon' => 'success',
        ]);

        return redirect()->route('admin.products.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        if ($product->inventories()->exists()) {
            session()->flash('swal', [
                'title' => 'Error',
                'text' => 'No se puede eliminar el producto porque está asociado a una orden.',
                'icon' => 'error',
            ]);
            return redirect()->route('admin.products.index');
        }

        if ($product->purchases()->exists() || $product->quotes()->exists()) {
            session()->flash('swal', [
                'title' => 'Error',
                'text' => 'No se puede eliminar el producto porque está asociado a una compra o cotización.',
                'icon' => 'error',
            ]);
            return redirect()->route('admin.products.index');
        }

        $product->images()->delete();
        $product->delete();
        session()->flash('swal', [
            'title' => 'Exitoso',
            'text' => 'El producto se ha eliminado correctamente.',
            'icon' => 'success',
        ]);

        return redirect()->route('admin.products.index');
    }

    public function uploadImages(Request $request, Product $product): \Symfony\Component\HttpFoundation\Response //: RedirectResponse
    {
        $tempPath = Storage::put('images/products', $request->file('file'));
        $extension = $request->file('file')->getClientOriginalExtension();
        $imagenProduct = $product->images()->create([
            'path' => $tempPath,
            'size' => $request->file('file')->getSize(),
            'alt_text' => $product->uuid . '.' . $extension,
        ]);
        $newFileName = $product->uuid . '_' . time() . '.' . $extension;
        $newPath = 'images/products/' . $newFileName;
        // MOVEMOS FÍSICAMENTE el archivo del nombre temporal al nuevo nombre
        Storage::move($tempPath, $newPath);
        // Actualizamos la BD con el nuevo path
        $imagenProduct->update([
            'path' => $newPath,
        ]);
        Log::info($imagenProduct->path);
        $imagenProduct->save();

        return response()->json([
            'uuid' => $imagenProduct->uuid,
            'path' => $imagenProduct->path,
        ])->setStatusCode(201);
    }
}
