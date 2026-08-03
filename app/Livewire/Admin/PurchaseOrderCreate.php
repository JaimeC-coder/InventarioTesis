<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\FileServices;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PurchaseOrderCreate extends Component
{
    public $voucher_type = 1;

    public $serie = 'OC-00001';

    public $correlativo;

    public $date = '';

    public $supplier_uuid = '';

    public $total = 0.00;

    public $observation = '';

    public $product_uuid = '';

    public $supplier_id;

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
        $this->correlativo = Purchase::where('serie', $this->serie)->max('correlativo') + 1;
        $this->date = now()->format('Y-m-d');
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
            'price' => $product->price_purchase,
            'subtotal' => 0,
        ];
        $this->reset('product_uuid');
    }

    public function save()
    {
        if (!empty($this->supplier_uuid)) {
            $supplierId = Supplier::where('uuid', $this->supplier_uuid)->value('id');
            $this->supplier_id = $supplierId; // ✅ asignas directo a la propiedad
        }

        $this->validate([
            'voucher_type' => 'required|in:1,2',
            'serie' => 'required|string|max:20',
            'correlativo' => 'required|integer|min:1',
            'date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
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
            'supplier_id' => 'Proveedor',
            'total' => 'Total',
            'observation' => 'observation',
            'products.*.id' => 'ID del producto',
            'products.*.quantity' => 'Cantidad del producto',
            'products.*.price' => 'Precio del producto',
        ]);
        //quiero que esto se tenga en una transacción
        $PurchaseOrder = PurchaseOrder::create([
            'voucher_type' => $this->voucher_type,
            'serie' => $this->serie,
            'correlativo' => $this->correlativo,
            'date' => $this->date,
            'supplier_id' => $this->supplier_id,
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
                'subtotal' => $product['quantity'] * $product['price'],
            ]);
        }

        $fileDirection = FileServices::generatePdfNow(['model' => PurchaseOrder::class, 'uuids' => $PurchaseOrder->uuid]);
        $PurchaseOrder->update(['file_path' => $fileDirection]);
        $PurchaseOrder->save();

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Orden de compra creada',
            'text' => 'La orden de compra se ha creado exitosamente.',
        ]);

        return redirect()->route('admin.purchases-orders.index');
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
        return view('livewire.admin.purchase-order-create');
    }
}
