<?php

namespace App\Livewire\Admin;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Quote;
use App\Services\FileServices;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class QuoteCreate extends Component
{
    public $voucher_type = 1;

    public $serie = 'OC-00001';

    public $correlativo;

    public $date = '';

    public $customer_uuid = '';

    public $total = 0.00;

    public $observation = '';

    public $product_uuid = '';

    public $customer_id;

    public $product_id;

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

            // $validator->after(function ($validator) {
            //     $total = 0;
            //     foreach ($this->products as $product) {
            //         $total += $product['quantity'] * $product['price'];
            //     }
            //     $this->total = $total;
            // });
        });
    }

    public function mount(): void
    {
        $this->correlativo = Quote::max('correlativo') + 1;
        $this->serie =  sprintf('OC-%04d', $this->correlativo);
        $this->date = now()->format('Y-m-d');
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
        $product = Product::where('uuid', $this->product_uuid)->first();
        if (!$product) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Producto no encontrado.',
            ]);
            $this->reset('product_uuid');
            return;
        }

        $exists = collect($this->products)->where('id', $product->id)->first();
        if ($exists) {
            $this->dispatch('swal', [
                'icon' => 'warning',
                'title' => 'Producto ya agregado',
                'text' => 'El producto ya ha sido agregado a la lista.',
            ]);
            $this->reset('product_id');
            return;
        }

        $priceA = (float) $product->price_sale_regular;
        $priceB = (float) $product->price_sale_a1;
        $priceType = 'GENERAL';
        $price = $priceA;
        if (!empty($this->customer_uuid)) {
            $customer = Customer::where('uuid', $this->customer_uuid)->first();
            if ($customer && isset($customer->type) && strtoupper($customer->type) === 'A1') {
                $priceType = 'A1';
                $price = $priceB;
            }
        }

        $this->products[] = [
            'id' => $product->id,
            'name' => $product->name,
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
        if (!empty($this->customer_uuid)) {
            $customerId = Customer::where('uuid', $this->customer_uuid)->value('id');
            $this->customer_id = $customerId; // ✅ asignas directo a la propiedad
        }

        // recalcular total en backend por seguridad
        $this->recalculateTotalFromProducts();
        $this->validate([
            'voucher_type' => 'required|in:1,2',
            'serie' => 'required|string|max:20',
            'correlativo' => 'required|integer|min:1',
            'date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'total' => 'required|numeric|min:0.01',
            'observation' => 'nullable|string|max:500',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
        ], [], [
            'voucher_type' => 'Tipo de comprobante',
            'serie' => 'Serie',
            'correlativo' => 'Correlativo',
            'date' => 'Fecha',
            'customer_id' => 'Cliente',
            'total' => 'Total',
            'observation' => 'observation',
            'products.*.id' => 'ID del producto',
            'products.*.quantity' => 'Cantidad del producto',
            'products.*.price' => 'Precio del producto',
        ]);
        DB::beginTransaction();
        try {
            //quiero que esto se tenga en una transacción
            $PurchaseOrder = Quote::create([
                'voucher_type' => $this->voucher_type,
                'serie' => $this->serie,
                'correlativo' => $this->correlativo,
                'date' => $this->date,
                'customer_id' => $this->customer_id,
                'subtotal' => $this->total,
                'igv' => $this->total * 0.18,
                'total' => $this->total * 1.18,
                'total_string' => $this->totalEnLetras($this->total * 1.18),
                'observation' => $this->observation,
                'user_id' => Auth::id(),
            ]);
            foreach ($this->products as $product) {
                $product_id = Product::where('id', $product['id'])->value('id');
                $PurchaseOrder->products()->attach($product_id, [
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'price_type' => 'QUOTE',
                    'subtotal' => $product['quantity'] * $product['price'],
                ]);
            }

            $fileDirection = FileServices::generatePdfNow(['model' => Quote::class, 'uuids' => $PurchaseOrder->uuid]);
            $PurchaseOrder->update(['file_path' => $fileDirection]);
            $PurchaseOrder->save();
            DB::commit();
            session()->flash('swal', [
                'icon' => 'success',
                'title' => 'Cotización creada',
                'text' => 'La cotización se ha creado exitosamente.',
            ]);

            return redirect()->route('admin.quotes.index');
        } catch (\Throwable $throwable) {
            DB::rollBack();
            // dispatch error
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error al crear la venta',
                'text' => $throwable->getMessage(),
            ]);
            // opcional: log error
            throw $throwable;
        }
    }

    protected function totalEnLetras($monto, $moneda = 'SOLES'): string
    {
        $numberFormatter = new \NumberFormatter('es', \NumberFormatter::SPELLOUT);
        $entero = floor($monto);
        $decimales = str_pad(round(($monto - $entero) * 100), 2, '0', STR_PAD_LEFT);

        return mb_strtoupper(
            $numberFormatter->format($entero) . sprintf(' %s CON %s/100', $moneda, $decimales)
        );
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.quote-create');
    }
}
