<?php

namespace App\Livewire\Admin\Create;

use App\Http\Requests\ProductRequest;
use App\Livewire\Concerns\ResolvesUuidsToIds;
use App\Models\Measure;
use App\Models\Product as ModelsProduct;
use App\Models\Unit;
use App\Traits\HandlesSwalMessagesTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Product extends Component
{
    use ResolvesUuidsToIds;

    use HandlesSwalMessagesTrait;

    public $measures_uuid = [];

    public $units_uuid = [];

    public $category_uuid;

    public $category_id;

    public $supplier_id;

    public $supplier_uuid;

    public $name;

    public $name_specific;

    public $description;

    public $codedisabled = false;

    public $locked = false;

    public $price_sale;

    public $price_purchase;

    public $productBaseName = '';

    public $stock;

    public $code;

    public $stock_min = 0;

    public $category_code = 0;

    public $products = [];

    public function limpiar(): void
    {
        $this->reset([
            'measures_uuid',
            'units_uuid',
            'category_uuid',
            'category_id',
            'supplier_uuid',
            'name',
            'name_specific',
            'description',
            'supplier_id',
            'price_sale',
            'price_purchase',
            'productBaseName',
            'stock',
            'code',
            'stock_min',
            'category_code',
            'products',
        ]);
        $this->locked = false;
        $this->codedisabled = false;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function updated(string $property, ?string $value): void
    {
        Log::info('Property updated: ' . $property . ' with value: ' . $value);
        if ($property === 'category_uuid' && ($value !== null && $value !== '' && $value !== '0')) {
            Log::info('Category UUID updated: ' . $value);
            $this->category_code = \App\Models\Category::where('uuid', $value)->value('codigo');
            $this->codedisabled = true;
        }

        if ($property === 'category_uuid' && ($value === null || $value === '' || $value === '0')) {
            $this->codedisabled = false;
            $this->category_code = 0;
        }
    }

    public function addProduct(): void
    {
        $this->validate([
            'supplier_uuid' => 'required|exists:suppliers,uuid',
            'category_uuid' => 'required|exists:categories,uuid',
            'category_code' => 'required',
            'code' => 'required',
            'name' => 'required',
            'name_specific' => 'nullable',
            'units_uuid' => 'required|array|min:1',
            'measures_uuid' => 'required|array|min:1',
        ]);
        $units = Unit::whereIn('uuid', $this->units_uuid)->get();
        $measures = Measure::whereIn('uuid', $this->measures_uuid)->get();
        $id = count($this->products) + 1;
        foreach ($units as $unit) {
            foreach ($measures as $measure) {
                // Concatenado: códigoUsuario-códigoUnidad-códigoMedida
                $codigoConcatenado = sprintf('%s%s%s%s', $this->category_code, $this->code, $unit->code, $measure->code);
                // Nombre: base + unidad + medida
                $nameespecificPart = $this->name_specific ? ' ' . $this->name_specific : '';
                $nombreFinal = sprintf('%s%s por %s de %s', $this->name, $nameespecificPart, $unit->name, $measure->name);
                $this->productBaseName = $this->name . $nameespecificPart;
                $this->products[] = [
                    'id' => $id++,
                    'codigo'        => $codigoConcatenado,
                    'name'          => $nombreFinal,
                    'price_sale'        => 0, // Inicialmente en 0, se puede editar en la tabla
                    'price_purchase'    => 0,
                    'unituuid'    => $unit->uuid, // Concatenado para identificar
                    'measureuuid' => $measure->uuid,
                    'unit' => $unit->name,
                    'measure' => $measure->name,
                ];
            }
        }

        $this->locked = true;
        $this->reset(['name', 'name_specific', 'units_uuid', 'measures_uuid']);
        Log::info('products después de addProduct: ' . json_encode($this->products));
    }

    public function removeProduct($id): void
    {
        $this->products = array_filter($this->products, fn(array $product): bool => $product['id'] !== $id);
        // Reindexar el array para evitar problemas con los IDs
        $this->products = array_values($this->products);
    }

    public function save()
    {
        $this->resolveSupplierId();
        $this->resolveCategoryId();
        Log::info('products:' . count($this->products));
        $productRequest = new ProductRequest();
        $this->validate($productRequest->rulesForAction('POST'), $productRequest->messages(), $productRequest->attributes());
        DB::beginTransaction();
        try {
            $productBaseid = ModelsProduct::create([
                'supplier_id' => $this->supplier_id,
                'name' => $this->productBaseName,
                'category_code' => $this->category_code,
                'code' => $this->code,
                'description' => $this->description,
                'price_sale' => 0,
                'price_purchase' => 0,
                'stock' => 0,
                'min_stock' => $this->stock_min,
                'is_active_product' => false,
                'category_id' =>  $this->category_id,
            ]);
            // luego vamos a crear los productos dependientes
            foreach ($this->products as $product) {
                ModelsProduct::create([
                    'supplier_id' => $this->supplier_id,
                    'name' => $product['name'],
                    'category_code' => $this->category_code,
                    'code' => $this->code,
                    'barcode' => $product['codigo'],
                    'description' => $this->description,
                    'price_sale' => $product['price_sale'],
                    'price_purchase' => $product['price_purchase'],
                    'stock' => 0,
                    'min_stock' => $this->stock_min,
                    'is_active_product' => true,
                    'product_base_id' => $productBaseid->id,
                    'category_id' =>  $this->category_id,
                    'unit_id' => \App\Models\Unit::where('uuid', $product['unituuid'])->value('id'),
                    'measure_id' => \App\Models\Measure::where('uuid', $product['measureuuid'])->value('id'),
                ]);
            }

            DB::commit();
            $this->dispatch('swal:success', [
                'title' => 'Productos guardados',
                'text' => 'Los productos se han guardado correctamente.',
                'icon' => 'success',
            ]);
            $this->limpiar();

            return redirect()->route('admin.products.index');
        } catch (\Illuminate\Validation\ValidationException  $throwable) {
            DB::rollBack();
            Log::error('Error al guardar productos - ValidationException: ' . $throwable->getMessage(), [
                'errors' => $throwable->errors(),
            ]);
            $this->dispatch('swal:success', [
                'title' => 'Error al guardar productos',
                'text' => 'Ocurrió un error al guardar los productos. Por favor, inténtelo de nuevo.',
                'icon' => 'error',
            ]);
            return redirect()->back();
        } catch (\Exception $throwable) {
            DB::rollBack();
            Log::error('Error al guardar productos - Exception: ' . $throwable->getMessage());
            $this->dispatch('swal:success', [
                'title' => 'Error al guardar productos',
                'text' => 'Ocurrió un error al guardar los productos. Por favor, inténtelo de nuevo.',
                'icon' => 'error',
            ]);
            return redirect()->back();
        } catch (\Throwable $throwable) {
            DB::rollBack();
            Log::error('Error al guardar productos - Throwable: ' . $throwable->getMessage());
            $this->dispatch('swal:success', [
                'title' => 'Error al guardar productos',
                'text' => 'Ocurrió un error al guardar los productos. Por favor, inténtelo de nuevo.',
                'icon' => 'error',
            ]);
            return redirect()->back();
        }
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.create.product');
    }
}
