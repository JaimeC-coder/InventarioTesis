<?php

namespace App\Livewire\Admin;

use App\Http\Requests\QuoteRequest;
use App\Livewire\Concerns\ResolvesUuidsToIds;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Quote;
use App\Services\ProductDetailServices;
use App\Services\UtilitisServices;
use App\Traits\HandlesSwalMessagesTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class QuoteCreate extends Component
{
    use ResolvesUuidsToIds;

    use HandlesSwalMessagesTrait;

    public $voucher_type = 1;

    public $serie = 'QT01';

    public $correlativo = 1;

    public $date = '';

    public $customer_uuid = '';

    public $customer_id;

    public $warehouse_uuid = '';

    public ?int $warehouse_id;

    public $total = 0.00;

    public $observation = '';

    public $product_uuid = '';

    public $product_id = 0;

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
            $this->errorSwal('Producto no encontrado.');
            $this->reset('product_uuid');
            return;
        }

        $exists = collect($this->products)->where('id', $product->id)->first();
        if ($exists) {
            $this->warningSwal('El producto ya ha sido agregado a la lista.');
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
        $this->resolveCustomerId();
        $this->resolveWarehouseId();
        // recalcular total en backend por seguridad
        $this->recalculateTotalFromProducts();
        $Quote = new QuoteRequest();
        $this->validate($Quote->rulesForAction('POST'), $Quote->messages(), $Quote->attributes());
        DB::beginTransaction();
        try {
            $correlativo = UtilitisServices::NextCorrelative(Quote::class);
            //quiero que esto se tenga en una transacción
            $Quote = Quote::create([
                'voucher_type' => $this->voucher_type,
                'serie' => $this->serie,
                'correlativo' => $correlativo,
                'date' => $this->date,
                'warehouse_id' => $this->warehouse_id,
                'customer_id' => $this->customer_id,
                'subtotal' => $this->total,
                'igv' => $this->total * 0.18,
                'total' => $this->total * 1.18,
                'total_string' => UtilitisServices::TotalEnLetras($this->total * 1.18),
                'observation' => $this->observation,
                'user_id' => Auth::id(),
            ]);
            ProductDetailServices::createDetailproductableCotizacion($Quote, $this->products);
            UtilitisServices::generateAndAttachPdf(Quote::class, $Quote);
            DB::commit();
            session()->flash('swal', [
                'icon' => 'success',
                'title' => 'Cotización creada',
                'text' => 'La cotización se ha creado exitosamente.',
            ]);

            return redirect()->route('admin.quotes.index');
        } catch (\Exception $throwable) {
            DB::rollBack();
            $this->errorSwal('Ocurrió un error al crear la cotización.');
            Log::error('Error al crear la cotización - Exception: ' . $throwable->getMessage());
        } catch (\Throwable $throwable) {
            DB::rollBack();
            $this->errorSwal('Ocurrió un error al crear la cotización.');
            Log::error('Error al crear la cotización - Throwable: ' . $throwable->getMessage());
        }

        return null;
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.quote-create');
    }
}
