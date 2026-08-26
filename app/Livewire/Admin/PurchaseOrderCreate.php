<?php

namespace App\Livewire\Admin;

use App\Http\Requests\PurchaseOrderRequest;
use App\Livewire\Concerns\ResolvesUuidsToIds;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Services\ProductDetailServices;
use App\Services\UtilitisServices;
use App\Traits\HandlesSwalMessagesTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class PurchaseOrderCreate extends Component
{
    use ResolvesUuidsToIds;

    use HandlesSwalMessagesTrait;

    public $voucher_type = 1;

    public $serie = 'OC01';

    public $correlativo = 1;

    public $date = '';

    public $supplier_uuid = '';

    public $warehouse_uuid = '';

    public ?int $supplier_id = null;

    public ?int $warehouse_id = null;

    public $total = 0.00;

    public $observation = '';

    public $product_uuid = '';

    public ?int $product_id = null;

    public $products = [];

    public function boot(): void
    {
        $this->withValidator(function ($validator): void {
            if ($validator->fails()) {
                $error = $validator->errors()->toArray();
                $html = "<ul class='list-disc list-inside space-y-2 text-gray-700'>";
                foreach ($error as $messages) {
                    foreach ($messages as $message) {
                        $html .= sprintf('<li>%s</li>', $message);
                    }
                }

                $html .= '</ul>';
                $this->dispatch('swal', [
                    'icon' => 'error',
                    'title' => 'Error',
                    'html' => $html,
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
            $this->reset('product_uuid');
            return;
        }

        $this->products = array_values($this->products);
        $this->products[] = [
            'id' => $product->id,
            'name' => $product->name,
            'quantity' => 1,
            'price' => $product->price_purchase,
            'subtotal' => 0,
        ];
        Log::info('Product added to purchase order: ', [
            'products' => $this->products,
        ]);
        $this->reset('product_uuid');
    }

    public function removeProduct(int $index): void
    {
        unset($this->products[$index]);
        $this->products = array_values($this->products);
    }

    public function save()
    {
        $this->resolveSupplierId();
        $this->resolveWarehouseId();
        $PurchaseOrder = new PurchaseOrderRequest();
        $this->validate($PurchaseOrder->rulesForAction('POST'), $PurchaseOrder->messages(), $PurchaseOrder->attributes());
        DB::beginTransaction();
        try {
            $correlativo = UtilitisServices::NextCorrelative(PurchaseOrder::class);
            $PurchaseOrder = PurchaseOrder::create([
                'voucher_type' => $this->voucher_type,
                'warehouse_id' => $this->warehouse_id,
                'serie' => $this->serie,
                'correlativo' => $correlativo,
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
            $this->successSwal('La orden de compra se ha creado exitosamente.', type: 'session');
            return redirect()->route('admin.purchases-orders.index');
        } catch (\Exception $throwable) {
            DB::rollBack();
            Log::error('Error al crear la orden de compra - Exception: ' . $throwable->getMessage());
            $this->errorSwal('Ocurrió un error al crear la orden de compra.');
            return redirect()->back();
        } catch (\Throwable $throwable) {
            DB::rollBack();
            Log::error('Error al crear la orden de compra - Throwable: ' . $throwable->getMessage());
            $this->errorSwal('Ocurrió un error al crear la orden de compra.');
            return redirect()->back();
        }
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.purchase-order-create');
    }
}
