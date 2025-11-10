<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Measure;
use App\Models\Product;
use App\Models\Unit;
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
            session()->flash('swal', [
                'title' => 'Exitoso',
                'text' => 'El producto se ha creado correctamente.',
                'icon' => 'success',
            ]);

            return redirect()->route('admin.products.index');
        } catch (\Exception $exception) {
            Log::info('Error al crear producto: ' . $exception->getMessage());
            session()->flash('swal', [
                'title' => 'Error',
                'text' => 'Hubo un problema al crear el producto.',
                'icon' => 'error',
            ]);

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
            session()->flash('swal', [
                'title' => 'Exitoso',
                'text' => 'El producto se ha actualizado correctamente.',
                'icon' => 'success',
            ]);
        } catch (\Exception $exception) {
            session()->flash('swal', [
                'title' => 'Error',
                'text' => 'Hubo un problema al actualizar el producto.',
                'icon' => 'error',
            ]);
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
        } catch (\Exception $exception) {
            session()->flash('swal', [
                'title' => 'Error',
                'text' => 'Hubo un problema al eliminar el producto.',
                'icon' => 'error',
            ]);
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

    public function massiveProducts(Request $request)
    {
        $products = [];
        foreach ($request['productos'] as $productData) {
            $productBase = Product::create([
                'name'          => strtoupper($productData['PRODUCTO']),
                'category_code' => $productData['CP'],
                'code'          => $productData['CODIGO'],
                'price_sale' => 0,
                'price_purchase' => 0,
                'stock' => 0,
                'min_stock' => 0,
            ]);
            $generatedProducts = $this->information10($productData, $productBase->id);
            //registrar productos en la base de datos
            foreach ($generatedProducts as $generatedProduct) {
                $existingProduct = Product::where('barcode', $generatedProduct['barcode'])->first();
                if (!$existingProduct) {
                    Product::create($generatedProduct);
                    $products[] = $generatedProduct; // Agrega el producto creado al array
                }
            }

            $products = array_merge($products, $generatedProducts);
        }

        return response()->json(['products' => $products], 200);
    }

    protected function information10($product, int $productBase): array
    {
        $measureLiquido = [1, 3, 5, 7, 8, 11, 13];
        $measureSolido = [2, 4, 6, 9, 10, 12, 14, 15, 16, 17, 18, 19, 20];
        $units = [1, 2, 3];
        if (in_array($product['CP'], [10, 20, 30, 40])) {
            return $this->arrayinfo($units, $measureLiquido, (array)$product, $productBase);
        }

        if (in_array($product['CP'], [50, 60, 70, 80, 90, 100, 101, 102])) {
            return $this->arrayinfo($units, $measureSolido, (array)$product, $productBase);
        }

        return []; // Retorna array vacío si no coincide
    }

    protected function arrayinfo(array $unitarry, array $measurearry, array $data, int $productBase): array
    {
        $products = []; // Inicializa el array ANTES del foreach
        $price_sale = 150;
        $price_purchase = 100;
        $units = Unit::whereIn('id', $unitarry)->get();
        $measures = Measure::whereIn('id', $measurearry)->get();
        foreach ($units as $unit) {
            foreach ($measures as $measure) {
                $codigoConcatenado = sprintf('%s%s%s%s', $data['CP'], $data['CODIGO'], $unit->code, $measure->code);
                $nombreFinal = sprintf('%s por %s de %s', $this->clearName($data['PRODUCTO'], $data['CODIGO']), $unit->name, $measure->description_for_product);
                $products[] = [
                    'barcode'        => $codigoConcatenado,
                    'name'          => strtoupper($nombreFinal),
                    'price_sale' => $price_sale,
                    'price_purchase' => $price_purchase,
                    'category_id' => 1,
                    'category_code' => $data['CP'],
                    'stock' => 100,
                    'min_stock' => 10,
                    'code' => $data['CODIGO'],
                    'unit_id' => $unit->id,
                    'measure_id' => $measure->id,
                    'productBase_id' => $productBase,
                ];
            }
        }

        return $products; // ¡Retorna el array!
    }

    protected function clearName(string $name, String $codigo): string
    {
        // busca y elimina el valor de $codigo en $name
        $name = str_ireplace($codigo, '', $name);
        // elimina espacios en blanco al inicio y al final
        $name = trim($name);
        LOG::info('Nombre limpiado: ' . $name);
        return $name;
    }
}
