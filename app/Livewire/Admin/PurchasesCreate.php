<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\FileServices;
use App\Services\KardexServices;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            'warehouse_uuid' => 'required|exists:warehouses,uuid',
        ]);
        $product = Product::where('uuid', $this->product_uuid)->first();
        Warehouse::where('uuid', $this->warehouse_uuid)->first();
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

        // $kardex = KardexServices::getLastRecord($product->id, $warehouse->id);
        $this->products[] = [
            'id' => $product->id,
            'name' => $product->name,
            'quantity' => 1,
            'price' => $product->price_purchase,
            'subtotal' => $product->price_purchase,
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
            'observation' => 'observation',
            'products.*.id' => 'ID del producto',
            'products.*.quantity' => 'Cantidad del producto',
            'products.*.price' => 'Precio del producto',
        ]);
        DB::beginTransaction();
        try {
            $Purchase = Purchase::create([
                'voucher_type' => $this->voucher_type,
                'serie' => $this->serie,
                'purchase_order_id' => $this->purchase_order_id,
                'correlativo' => $this->correlativo,
                'date' => $this->date,
                'warehouse_id' => $this->warehouse_id,
                'supplier_id' => $this->supplier_id,
                'subtotal' => $this->total,
                'igv' => $this->total * 0.18,
                'total' => $this->total * 1.18,
                'total_string' => $this->totalEnLetras($this->total * 1.18),
                'user_id' => auth()->id(),
                'observation' => $this->observation,
            ]);
            foreach ($this->products as $product) {
                $product_id = Product::where('id', $product['id'])->value('id');
                $Purchase->products()->attach($product_id, [
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'subtotal' => $product['quantity'] * $product['price'],
                ]);
                KardexServices::registerEntry($Purchase, $product, $this->warehouse_id, 'Compra ID: ' . $Purchase->id);
            }

            $fileDirection = FileServices::generatePdfNow(['model' => Purchase::class, 'uuids' => $Purchase->uuid]);
            $Purchase->update(['file_path' => $fileDirection]);
            $Purchase->save();
            Log::info('File generated at: ' . $fileDirection);
            DB::commit();
            session()->flash('swal', [
                'icon' => 'success',
                'title' => 'Compra creada',
                'text' => 'La compra se ha creado exitosamente.',
            ]);
            return redirect()->route('admin.purchases.index');
        } catch (\Throwable $throwable) {
            DB::rollBack();
            Log::error('Error creating purchase: ' . $throwable->getMessage());
            session()->flash('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Ocurrió un error al crear la compra.',
            ]);
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
        return view('livewire.admin.purchases-create');
    }
}
