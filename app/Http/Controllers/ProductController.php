<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Measure;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $validated = $request->validate([
            'categoria'              => ['required', 'integer'],
            'medidas'                => ['required', 'string'],
            'productos'               => ['required', 'array'],
            'productos.*.PRODUCTO'    => ['required', 'string'],
            'productos.*.CODIGO'      => ['required', 'string'],
        ], [
            'categoria.required' => 'El campo categoría es obligatorio.',
            'categoria.integer' => 'El campo categoría debe ser un número entero.',
            'medidas.required' => 'El campo medidas es obligatorio.',
            'medidas.string' => 'El campo medidas debe ser una cadena de texto.',
            'productos.required' => 'El campo productos es obligatorio.',
            'productos.array' => 'El campo productos debe ser un arreglo.',
            'productos.*.PRODUCTO.required' => 'El nombre del producto es obligatorio.',
            'productos.*.PRODUCTO.string' => 'El nombre del producto debe ser una cadena de texto.',
            'productos.*.CODIGO.required' => 'El código del producto es obligatorio.',
            'productos.*.CODIGO.string' => 'El código del producto debe ser una cadena de texto.',
        ]);
        $units = Unit::all(['id', 'code', 'abbreviation'])->makeVisible('id')->toArray();
        $measures = Measure::where('category', $validated['medidas'])->select('id', 'code', 'description_for_product')->get()->makeVisible('id')->toArray();
        $categorie = Category::where('codigo', $validated['categoria'])->select('id', 'codigo')->firstOrFail();
        $allGeneratedProducts = [];
        DB::transaction(function () use ($validated, $categorie, $measures, $units, &$allGeneratedProducts): void {
            foreach ($validated['productos'] as $productData) {
                $productBase = Product::create([
                    'name'          => strtoupper($productData['PRODUCTO']),
                    'code'          => $productData['CODIGO'],
                    'category_code' => $categorie['codigo'],
                    'description' => 'Producto base para ' . $productData['PRODUCTO'],
                    'price_sale_a1' => 0,
                    'price_sale_regular' => 0,
                    'price_purchase' => 0,
                    'stock' => 0,
                    'min_stock' => 0,
                    'is_active_product' => 0,
                    'category_id' => $categorie->id,
                ]);
                $allGeneratedProducts = array_merge(
                    $allGeneratedProducts,
                    $this->buildProductVariants($units, $categorie, $measures, $productData, $productBase->id)
                );
            }

            $barcodes = array_column($allGeneratedProducts, 'barcode');
            $existingBarcodes = Product::whereIn('barcode', $barcodes)->pluck('barcode')->toArray();
            $newProducts = array_values(array_filter(
                $allGeneratedProducts,
                fn(array $p): bool => !in_array($p['barcode'], $existingBarcodes)
            ));
            if ($newProducts !== []) {
                $now = now();
                $rows = array_map(fn(array $p): array => [
                    ...$p,
                    'created_at' => $now,
                    'updated_at' => $now,
                ], $newProducts);
                foreach (array_chunk($rows, 500) as $chunk) {
                    Product::insert($chunk);
                }
            }
        });
        return response()->json(['products' => $allGeneratedProducts], 200);
    }

    protected function buildProductVariants(array $units, Category $category, array $measures, array $productData, int $productBase): array
    {
        $products = []; // Inicializa el array ANTES del foreach
        $price_sale_regular = rand(70, 200);
        $price_sale = rand(50, 160);
        $price_purchase = rand(70, 200);
        foreach ($units as $unit) {
            foreach ($measures as $measure) {
                $codigoConcatenado = sprintf('%s%s%s%s', $category->codigo, $productData['CODIGO'], $measure['code'], $unit['code']);
                $nombreFinal = sprintf('%s %s', $this->clearName($productData['PRODUCTO'], $productData['CODIGO']), $measure['description_for_product']);
                $products[] = [
                    'name'          => strtoupper($nombreFinal),
                    'code' => $productData['CODIGO'],
                    'category_code' => $category->codigo,
                    'barcode'        => $codigoConcatenado,
                    'description' => $nombreFinal . ' por ' . $unit['abbreviation'],
                    'price_sale_regular' => $price_sale_regular,
                    'price_sale_a1' => $price_sale,
                    'price_purchase' => $price_purchase,
                    'stock' => 100,
                    'min_stock' => 10,
                    'is_active_product' => 1,
                    'productBase_id' => $productBase,
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'category_id' => $category->id,
                    'unit_id' => $unit['id'],
                    'measure_id' => $measure['id'],
                ];
            }
        }

        return $products;
    }

    protected function clearName(string $name, String $codigo): string
    {
        // busca y elimina el valor de $codigo en $name
        $name = str_ireplace($codigo, '', $name);
        // elimina espacios en blanco al inicio y al final
        $name = trim($name);
        return $name;
    }
}
