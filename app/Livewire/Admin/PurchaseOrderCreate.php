<?php

namespace App\Livewire\Admin;

use App\Http\Requests\PurchaseOrderRequest;
use App\Livewire\Concerns\ResolvesUuidsToIds;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Services\ProductDetailServices;
use App\Services\UtilitisServices;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class PurchaseOrderCreate extends Component
{
    use ResolvesUuidsToIds;

    public $voucher_type = 1;

    public $serie = 'OC01';

    public $correlativo;

    public $date = '';

    public $supplier_uuid = '';

    public $total = 0.00;

    public $observation = '';

    public $product_uuid = '';

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
        });
    }

    public function mount(): void
    {
        $this->correlativo = PurchaseOrder::max('correlativo') + 1;
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
        $this->resolveSupplierId();
        $PurchaseOrder = new PurchaseOrderRequest();
        $this->validate($PurchaseOrder->rulesForAction('POST'), $PurchaseOrder->messages(), $PurchaseOrder->attributes());
        DB::beginTransaction();
        try {
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
                'total_string' => UtilitisServices::TotalEnLetras($this->total * 1.18),
                'observation' => $this->observation,
                'user_id' => Auth::id(),
            ]);
            ProductDetailServices::createDetailproductableOrdenCompra($PurchaseOrder, $this->products);
            UtilitisServices::generateAndAttachPdf(PurchaseOrder::class, $PurchaseOrder);
            DB::commit();
            session()->flash('swal', [
                'icon' => 'success',
                'title' => 'Orden de compra creada',
                'text' => 'La orden de compra se ha creado exitosamente.',
            ]);

            return redirect()->route('admin.purchases-orders.index');
        } catch (\Throwable $throwable) {
            DB::rollBack();
            //throw $th;
            Log::error('Error creating purchase Order: ' . $throwable->getMessage());
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
        return view('livewire.admin.purchase-order-create');
    }
}
