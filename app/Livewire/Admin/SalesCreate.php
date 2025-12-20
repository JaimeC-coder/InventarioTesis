<?php

namespace App\Livewire\Admin;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Quote;
use App\Models\Sale;
use App\Models\Warehouse;
use App\Services\KardexServices;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class SalesCreate extends Component
{
    public $voucher_type = 2;

    public $serie = 'OC-00001';

    public $correlativo;

    public $date = '';

    public $customer_uuid = '';

    public $quote_uuid = '';

    public $total = 0.00;

    public $observation = '';

    public $product_uuid = '';

    public $customer_id;

    public $quote_id;

    public $product_id;

    public $warehouse_uuid = '';

    public $warehouse_id;

    public $products = [];

    public function boot(): void
    {
        $this->withValidator(function ($validator): void {
            if ($validator->fails()) {
                $error = $validator->errors()->toArray();
                $html = "<ul class='text-left'>";
                foreach ($error as $messages) {
                    foreach ($messages as $message) {
                        $html .= sprintf('<li>%s</li>', $message[0]);
                    }
                }

                $html .= '</ul>';
                $this->dispatch('swal', [
                    'icon' => 'error',
                    'title' => 'Error',
                    'text' => $html,
                ]);
            }
        });
    }

    public function mount(): void
    {
        $this->correlativo = Sale::max('correlativo') + 1;
        $this->serie =  sprintf('OC-%04d', $this->correlativo);
        $this->date = now()->format('Y-m-d');
    }

    public function updated($property, $value): void
    {
        // cuando cambie la cotización
        if ($property === 'quote_uuid' && !empty($value)) {
            $quote = Quote::where('uuid', $value)->first();
            if ($quote) {
                $this->voucher_type = $quote->voucher_type;
                $this->quote_id = $quote->id;
                $this->customer_uuid = $quote->customer->uuid;
                $this->customer_id = $quote->customer->id;
                $this->products = $quote->products->map(function ($product): array {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'quantity' => $product->pivot->quantity,
                        // usar exactamente el precio que vino en la cotización (inmutable)
                        'price' => (float) $product->pivot->price,
                        'subtotal' => $product->pivot->quantity * $product->pivot->price,
                        'price_type' => 'QUOTE',
                    ];
                })->toArray();
                // actualizar total
                // dd($this->products);
                $this->recalculateTotalFromProducts();
            }
        }

        // cuando cambie cliente: solo actualizar customer_id
        if ($property === 'customer_uuid' && !empty($value)) {
            $this->customer_id = Customer::where('uuid', $value)->value('id');
        }
    }

    protected function recalculateTotalFromProducts(): void
    {
        $sum = 0;
        foreach ($this->products as $product) {
            $qty = isset($product['quantity']) ? (int)$product['quantity'] : 0;
            $price = isset($product['price']) ? (float)$product['price'] : 0.0;
            $sum += $qty * $price;
        }

        // mantener 2 decimales
        $this->total = (float) number_format($sum, 2, '.', '');
    }

    public function addProduct(): void
    {
        $this->validate([
            'product_uuid' => 'required|exists:products,uuid',
        ]);
        $productModel = Product::where('uuid', $this->product_uuid)->first();
        if (!$productModel) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Producto no encontrado.',
            ]);
            $this->reset('product_uuid');
            return;
        }

        // evitar duplicados por id
        $exists = collect($this->products)->where('id', $productModel->id)->first();
        if ($exists) {
            $this->dispatch('swal', [
                'icon' => 'warning',
                'title' => 'Producto ya agregado',
                'text' => 'El producto ya ha sido agregado a la lista.',
            ]);
            $this->reset('product_uuid');
            return;
        }

        // obtener precios a/b del producto
        $priceA = (float) $productModel->price_sale_regular;
        $priceB = (float) $productModel->price_sale_a1;
        // Determinar tipo de cliente (si existe) y asignar precio por defecto
        $priceType = 'GENERAL';
        $price = $priceA;
        if (!empty($this->customer_uuid)) {
            $customer = Customer::where('uuid', $this->customer_uuid)->first();
            if ($customer && isset($customer->type) && strtoupper($customer->type) === 'A1') {
                $priceType = 'A1';
                $price = $priceB;
            }
        }

        // agregar producto con estructura extendida
        $this->products[] = [
            'id' => $productModel->id,
            'name' => $productModel->name,
            'quantity' => 1,
            'price' => $price,
            'price_a' => $priceA,
            'price_b' => $priceB,
            'price_type' => $priceType,
            'subtotal' => $price,
        ];
        $this->reset('product_uuid');
        $this->recalculateTotalFromProducts();
    }

    public function save()
    {
        // resolver ids relacionados
        if (!empty($this->customer_uuid)) {
            $this->customer_id = Customer::where('uuid', $this->customer_uuid)->value('id');
        }

        if (!empty($this->quote_uuid)) {
            $this->quote_id = Quote::where('uuid', $this->quote_uuid)->value('id');
        }

        if (!empty($this->warehouse_uuid)) {
            $this->warehouse_id = Warehouse::where('uuid', $this->warehouse_uuid)->value('id');
        }
        // recalcular total en backend por seguridad
        $this->recalculateTotalFromProducts();
        // validaciones
        $this->validate([
            'voucher_type' => 'required|in:1,2',
            'serie' => 'required|string|max:20',
            'correlativo' => 'required|integer|min:1',
            'date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quote_id' => 'nullable|exists:quotes,id',
            'total' => 'required|numeric|min:0.01',
            'observation' => 'nullable|string|max:500',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
            'products.*.price_type' => 'nullable|in:GENERAL,A1,QUOTE',
        ], [], [
            'voucher_type' => 'Tipo de comprobante',
            'warehouse_id' => 'ID del almacén',
            'quote_id' => 'ID de la cotización',
            'serie' => 'Serie',
            'correlativo' => 'Correlativo',
            'date' => 'Fecha',
            'customer_id' => 'Cliente',
            'total' => 'Total',
            'observation' => 'Observaciones',
            'products.*.id' => 'ID del producto',
            'products.*.quantity' => 'Cantidad del producto',
            'products.*.price' => 'Precio del producto',
            'products.*.price_type' => 'Tipo de precio del producto',
        ]);
        DB::beginTransaction();
        try {
            $Sale = Sale::create([
                'voucher_type' => $this->voucher_type,
                'serie' => $this->serie,
                'quote_id' => $this->quote_id,
                'correlativo' => $this->correlativo,
                'date' => $this->date,
                'warehouse_id' => $this->warehouse_id,
                'customer_id' => $this->customer_id,
                'total' => $this->total,
                'observation' => $this->observation,
            ]);
            Log::info('Venta creada con ID: ' . $Sale->id);
            foreach ($this->products as $product) {
                $product_id = Product::where('id', $product['id'])->value('id');
                // precio final utilizado (si from_quote es true, ya está el precio del pivot)
                $finalPrice = (float) $product['price'];
                $quantity = (int) $product['quantity'];
                $subtotal = $quantity * $finalPrice;
                $Sale->products()->attach($product_id, [
                    'quantity' => $quantity,
                    'price' => $finalPrice,
                    'price_type' => $product['price_type'] ?? 'GENERAL',
                    'subtotal' => $subtotal,
                ]);
                // registrar salida en kardex
                KardexServices::registerExit($Sale, $product, $this->warehouse_id, 'Venta ID: ' . $Sale->id);
                // Si necesitas almacenar inventario como antes, puedes reusar tu lógica aquí
                // (he dejado comentada tu lógica previa por si quieres activarla)
            }

            DB::commit();
            session()->flash('swal', [
                'icon' => 'success',
                'title' => 'Venta creada',
                'text' => 'La venta se ha creado exitosamente.',
            ]);

            return redirect()->route('admin.sales.index');
        } catch (\Throwable $throwable) {
            DB::rollBack();
            // dispatch error
            Log::error('Error al crear la venta: ' . $throwable->getMessage());
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error al crear la venta',
                'text' => "Ha ocurrido un error inesperado al crear la venta. Por favor, inténtelo de nuevo.",
            ]);
            // opcional: log error
            Log::error('Error al crear la venta: ' . $throwable->getMessage());
            throw $throwable;
        }
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.sales-create');
    }
}
