<?php

namespace App\Livewire\Admin;

use App\Http\Requests\PurchaseRequest;
use App\Livewire\Concerns\ResolvesUuidsToIds;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseOrder;
use App\Services\ProductDetailServices;
use App\Services\UtilitisServices;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class PurchasesCreate extends Component
{

    use ResolvesUuidsToIds;

    public $voucher_type = 2;

    public $serie = 'CM01';

    public $correlativo = 1;

    public $date = '';

    public $supplier_uuid = '';

    public $purchase_order_uuid = '';

    public $total = 0.00;

    public $observation = '';

    public $product_uuid = '';

    public $product_id = 0;

    public $warehouse_uuid = '';

    public $payment_method = 'EFECTIVO';

    public $payment_type = 'CONTADO';

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
        $this->correlativo = Purchase::max('correlativo') + 1;
        $this->date = now()->format('Y-m-d');
    }

    public function updated($property, $value): void
    {
        if ($property === 'purchase_order_uuid' && !empty($value)) {
            $this->loadFromPurchaseOrder($value);
        }
    }

    private function loadFromPurchaseOrder(string $uuid): void
    {
        $purchaseOrder = PurchaseOrder::where('uuid', $uuid)->first();
        if (!$purchaseOrder) {
            return;
        }

        $this->voucher_type = $purchaseOrder->voucher_type;
        $this->purchase_order_id = $purchaseOrder->id;
        $this->supplier_uuid = $purchaseOrder->supplier->uuid;
        $this->supplier_id = $purchaseOrder->supplier->id;
        $this->products = $purchaseOrder->products->map(fn($product): array => [
            'id' => $product->id,
            'name' => $product->name,
            'quantity' => $product->pivot->quantity,
            'price' => $product->pivot->price,
            'subtotal' => $product->pivot->quantity * $product->pivot->price,
        ])->toArray();
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
            'price_type' => 'COMPRA',
            'price' => $product->price_purchase,
            'subtotal' => $product->price_purchase,
        ];
        $this->reset('product_uuid');
    }

    public function save()
    {
        $this->resolveSupplierId();
        $this->resolvePurchaseOrderId();
        $this->resolveWarehouseId();

        $purchaseRequest = new PurchaseRequest();
        $this->validate($purchaseRequest->rulesForAction('POST'), $purchaseRequest->messages(), $purchaseRequest->attributes());
        DB::beginTransaction();
        try {

            $correlativo = UtilitisServices::NextCorrelative(PurchaseOrder::class);

            $Purchase = Purchase::create([
                'voucher_type' => $this->voucher_type,
                'serie' => $this->serie,
                'purchase_order_id' => $this->purchase_order_id,
                'correlativo' => $correlativo,
                'date' => $this->date,
                'warehouse_id' => $this->warehouse_id,
                'supplier_id' => $this->supplier_id,
                'subtotal' => $this->total,
                'igv' => $this->total * 0.18,
                'total' => $this->total * 1.18,
                'total_string' => UtilitisServices::TotalEnLetras($this->total * 1.18),
                'user_id' => Auth::id(),
                'observation' => $this->observation,
                'payment_method' => $this->payment_method,
                'payment_type' => $this->payment_type,
            ]);
            ProductDetailServices::createDetailproductableRegister($Purchase, $this->products, $this->warehouse_id, 'Compra ID: ' . $Purchase->id);
            UtilitisServices::generateAndAttachPdf(Purchase::class, $Purchase);

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



    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        $comprobante = [
            ['id' => 1, 'name' => 'Factura'],
            ['id' => 2, 'name' => 'Boleta'],
        ];
        $metodo_pago = [
            ['id' => 'EFECTIVO', 'name' => 'Efectivo'],
            ['id' => 'TARJETA', 'name' => 'Tarjeta'],
            ['id' => 'TRANSFERENCIA', 'name' => 'Transferencia'],
            ['id' => 'YAPE', 'name' => 'Yape'],
            ['id' => 'PLIN', 'name' => 'Plin'],
        ];
        $tipo_pago = [
            ['id' => 'CONTADO', 'name' => 'Contado'],
            ['id' => 'CREDITO', 'name' => 'Crédito'],
        ];

        return view('livewire.admin.purchases-create', ['comprobante' => $comprobante, 'metodo_pago' => $metodo_pago, 'tipo_pago' => $tipo_pago]);
    }
}
