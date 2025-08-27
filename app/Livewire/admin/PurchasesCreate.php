<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Warehouse;
use Livewire\Component;

class PurchasesCreate extends Component
{
    public $voucher_type = 2;

    public $serie = 'OC-00001';

    public $correlativo;

    public $date = '';

    public $supplier_uuid = '';

    public $purchase_order_uuid = '';

    public $total = 0.00;

    public $observation = '';

    public $product_uuid = '';

    public $supplier_id;

    public $purchase_order_id;

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
        $this->correlativo = Purchase::where('serie', $this->serie)->max('correlativo') + 1;
        $this->date = now()->format('Y-m-d');
    }

    public function updated($property, $value): void
    {
        if ($property === 'purchase_order_uuid' && !empty($value)) {
            $purchaseOrder = PurchaseOrder::where('uuid', $value)->first();
            if ($purchaseOrder) {
                $this->voucher_type = $purchaseOrder->voucher_type;
                $this->purchase_order_id = $purchaseOrder->id;
                $this->supplier_uuid = $purchaseOrder->supplier->uuid;
                $this->supplier_id = $purchaseOrder->id;
                $this->products = $purchaseOrder->products->map(function ($product): array {
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
            'price' => 0,
            'subtotal' => 0,
        ];
        $this->reset('product_uuid');
    }

    public function save()
    {
        if (!empty($this->supplier_uuid)) {
            $this->supplier_id = Supplier::where('uuid', $this->supplier_uuid)->value('id');
        }

        if (!empty($this->purchase_order_uuid)) {
            $this->purchase_order_id = PurchaseOrder::where('uuid', $this->purchase_order_uuid)->value('id');
        }

        if (!empty($this->warehouse_uuid)) {
            $this->warehouse_id = Warehouse::where('uuid', $this->warehouse_uuid)->value('id');
        }

        $this->validate([
            'voucher_type' => 'required|in:1,2',
            'serie' => 'required|string|max:20',
            'correlativo' => 'required|integer|min:1',
            'date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'total' => 'required|numeric|min:0.01',
            'observation' => 'nullable|string|max:500',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
        ], [], [
            'voucher_type' => 'Tipo de comprobante',
            'warehouse_id' => 'ID del almacén',
            'purchase_order_id' => 'ID de la orden de compra',
            'serie' => 'Serie',
            'correlativo' => 'Correlativo',
            'date' => 'Fecha',
            'supplier_id' => 'Proveedor',
            'total' => 'Total',
            'observation' => 'Observaciones',
            'products.*.id' => 'ID del producto',
            'products.*.quantity' => 'Cantidad del producto',
            'products.*.price' => 'Precio del producto',
        ]);
        //quiero que esto se tenga en una transacción
        $Purchase = Purchase::create([
            'voucher_type' => $this->voucher_type,
            'serie' => $this->serie,
            'purchase_order_id' => $this->purchase_order_id,
            'correlativo' => $this->correlativo,
            'date' => $this->date,
            'warehouse_id' => $this->warehouse_id,
            'supplier_id' => $this->supplier_id,
            'total' => $this->total,
            'observation' => $this->observation,
        ]);
        foreach ($this->products as $product) {
            $product_id = Product::where('id', $product['id'])->value('id');
            $Purchase->products()->attach($product_id, [
                'quantity' => $product['quantity'],
                'price' => $product['price'],
                'subtotal' => $product['quantity'] * $product['price'],
            ]);
        }

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Compra creada',
            'text' => 'La compra se ha creado exitosamente.',
        ]);

        return redirect()->route('admin.purchases.index');
    }

    public function render()
    {
        return view('livewire.admin.purchases-create');
    }
}
