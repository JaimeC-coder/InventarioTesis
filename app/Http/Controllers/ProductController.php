<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Traits\HandlesSwalMessagesTrait;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    use HandlesSwalMessagesTrait;

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
        $categories = \App\Models\Category::select('name', 'uuid')->get(); // Fetch all categories if needed

        return view('admin.products.create', ['categories' => $categories]); // Assuming you have a view for creating products
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $productRequest): RedirectResponse
    {
        //dd($productRequest->validated());
        try {
            // Validate and create the product
            Product::create($productRequest->validated());
            $this->successSwal('El producto se ha creado correctamente.', type: 'session');

            return redirect()->route('admin.products.index');
        } catch (\Exception $exception) {
            Log::info('Error al crear producto: ' . $exception->getMessage());
            $this->successSwal('Hubo un problema al crear el producto.', type: 'session');

            return redirect()->route('admin.products.index');
        }
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
        $categories = \App\Models\Category::select('name', 'uuid')->get(); // Fetch all categories if needed
        return view('admin.products.edit', ['product' => $product, 'categories' => $categories]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $productRequest, Product $product): RedirectResponse
    {
        try {
            // Validate and update the product
            $product->update($productRequest->validated());
            $this->successSwal('El producto se ha actualizado correctamente.', type: 'session');
        } catch (\Exception $exception) {
            $this->successSwal('Hubo un problema al actualizar el producto.', type: 'session');
        }

        return redirect()->route('admin.products.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        try {
            if ($product->inventories()->exists()) {
                $this->successSwal('No se puede eliminar el producto porque está asociado a una orden.', type: 'session');
                return redirect()->route('admin.products.index');
            }

            if ($product->purchases()->exists() || $product->quotes()->exists()) {
                $this->successSwal('No se puede eliminar el producto porque está asociado a una compra o cotización.', type: 'session');
                return redirect()->route('admin.products.index');
            }

            $product->images()->delete();
            $product->delete();
            $this->successSwal('El producto se ha eliminado correctamente.', type: 'session');

            return redirect()->route('admin.products.index');
        } catch (\Exception $exception) {
            $this->successSwal('Hubo un problema al eliminar el producto.', type: 'session');
        }

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
        $imagenProduct->save();

        return response()->json([
            'uuid' => $imagenProduct->uuid,
            'path' => $imagenProduct->path,
        ])->setStatusCode(201);
    }
}
