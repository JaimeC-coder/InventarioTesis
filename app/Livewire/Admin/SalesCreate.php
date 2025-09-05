<?php

namespace App\Livewire\Admin;

use App\Models\Customer;
use App\Models\Inventorie;
use App\Models\Product;
use App\Models\Quote;
use App\Models\Sale;
use App\Models\Warehouse;
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
        $this->correlativo = Quote::where('serie', $this->serie)->max('correlativo') + 1;
        $this->date = now()->format('Y-m-d');
    }

    public function updated($property, $value): void
    {
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
                        'price' => $product->pivot->price,
                        'subtotal' => $product->pivot->quantity * $product->pivot->price,
                    ];
                })->toArray();
            }
        }
    }

    public function addProduct(): void
    {
        $this->validate([
            'product_uuid' => 'required|exists:products,uuid',
        ]);
        $product = Product::where('uuid', $this->product_uuid)->first();
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

        $this->products[] = [
            'id' => $product->id,
            'name' => $product->name,
            'quantity' => 1,
            'price' => $product->price,
            'subtotal' => 0,
        ];
        $this->reset('product_uuid');
    }

    public function save()
    {
        if (!empty($this->customer_uuid)) {
            $this->customer_id = Customer::where('uuid', $this->customer_uuid)->value('id');
        }

        if (!empty($this->quote_uuid)) {
            $this->quote_id = Quote::where('uuid', $this->quote_uuid)->value('id');
        }

        if (!empty($this->warehouse_uuid)) {
            $this->warehouse_id = Warehouse::where('uuid', $this->warehouse_uuid)->value('id');
        }

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
        ]);
        //quiero que esto se tenga en una transacción
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
        foreach ($this->products as $product) {
            $product_id = Product::where('id', $product['id'])->value('id');
            $Sale->products()->attach($product_id, [
                'quantity' => $product['quantity'],
                'price' => $product['price'],
                'subtotal' => $product['quantity'] * $product['price'],
            ]);
            $lastrecortd = Inventorie::where('product_id', $product_id)
                ->where('warehouse_id', $this->warehouse_id)
                ->latest()
                ->first();
            $lastQuantity = $lastrecortd ? $lastrecortd->quantity_balance : 0;
            $lastTotal = $lastrecortd ? $lastrecortd->total_balance : 0;
            $lastcostBalance = $lastrecortd ? $lastrecortd->cost_balance : 0;
            $newQuantity = $lastQuantity - $product['quantity'];
            $newTotal = $lastTotal - ($product['quantity'] * $lastcostBalance);
            //$costBalance = $newQuantity > 0 ? $newTotal / $newQuantity : 0;
            $costBalance = $newTotal / ($newQuantity ?: 1);
            $Sale->inventories()->create([
                'detail' => 'Venta ID: ' . $Sale->id,
                'cost_out' => $lastcostBalance,
                'total_out' => $product['quantity'] * $lastcostBalance,
                'quantity_out' => $product['quantity'],
                'quantity_balance' => $newQuantity,
                'cost_balance' => $costBalance,
                'total_balance' => $newTotal,
                'product_id' => $product_id,
                'warehouse_id' => $this->warehouse_id,
            ]);
        }

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Venta creada',
            'text' => 'La venta se ha creado exitosamente.',
        ]);

        return redirect()->route('admin.sales.index');
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.sales-create');
    }
}
