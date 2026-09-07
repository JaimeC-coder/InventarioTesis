<?php

namespace App\Livewire\Admin\Create;

use App\Http\Requests\SaleRequest;
use App\Livewire\Concerns\ResolvesUuidsToIds;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Quote;
use App\Models\Sale as ModelsSale;
use App\Services\ProductDetailServices;
use App\Services\UtilitisServices;
use App\Traits\HandlesSwalMessagesTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Sale extends Component
{
    use ResolvesUuidsToIds;

    use HandlesSwalMessagesTrait;

    public $voucher_type = 2;

    public $serie = 'VN01';

    public $correlativo = 1;

    public $date = '';

    public $customer_uuid = '';

    public ?int $customer_id;

    public $quote_uuid = '';

    public ?int $quote_id;

    public $total = 0.00;

    public $observation = '';

    public $product_uuid = '';

    public $product_id = 0;

    public $warehouse_uuid = '';

    public ?int $warehouse_id;

    public $payment_method = 'EFECTIVO';

    public $payment_type = 'CONTADO';

    public $products = [];

    public function limpiar(): void
    {
        $this->reset([
            'customer_uuid',
            'customer_id',
            'quote_id',
            'quote_uuid',
            'total',
            'product_uuid',
            'warehouse_uuid',
            'warehouse_id',
            'product_id',
            'products',
            'observation',
        ]);
        $this->resetErrorBag();
        $this->resetValidation();
    }

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
        $this->correlativo = ModelsSale::max('correlativo') + 1;
        $this->date = now()->format('Y-m-d');
    }

    public function updated($property, $value): void
    {
        // cuando cambie la cotización
        if ($property === 'quote_uuid' && !empty($value)) {
            $this->loadFromQuote($value);
        }

        // cuando cambie cliente: solo actualizar customer_id
        if ($property === 'customer_uuid' && !empty($value)) {
            $this->customer_id = Customer::where('uuid', $value)->value('id');
        }
    }

    public function loadFromQuote(string $uuid): void
    {
        $quote = Quote::where('uuid', $uuid)->first();
        if (!$quote) {
            return;
        }

        $this->voucher_type = $quote->voucher_type;
        $this->quote_id = $quote->id;
        $this->customer_uuid = $quote->customer->uuid;
        $this->customer_id = $quote->customer->id;
        $this->warehouse_uuid = $quote->warehouse->uuid;
        $this->warehouse_id = $quote->warehouse->id;
        $this->observation = sprintf('Esta compra fue generada a partir de una cotización %s - ', $quote->serie) . UtilitisServices::completeCorrelativo($quote->correlativo);
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
        $this->recalcularTotalDesdeProductos();
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
        $this->recalcularTotalDesdeProductos();
    }

    public function save()
    {
        $this->resolveCustomerId();
        $this->resolveWarehouseId();
        $this->resolveQuoteId();
        $this->recalcularTotalDesdeProductos();
        $Sale = new SaleRequest();
        $this->validate($Sale->rulesForAction('POST'), $Sale->messages(), $Sale->attributes());
        DB::beginTransaction();
        try {
            $correlativo = UtilitisServices::NextCorrelative(ModelsSale::class);
            $Sale = ModelsSale::create([
                'voucher_type' => $this->voucher_type,
                'serie' => $this->serie,
                'quote_id' => $this->quote_id,
                'correlativo' => $correlativo,
                'date' => $this->date,
                'warehouse_id' => $this->warehouse_id,
                'customer_id' => $this->customer_id,
                'subtotal' => $this->total,
                'igv' => $this->total * 0.18,
                'total' => $this->total * 1.18,
                'total_string' => UtilitisServices::TotalEnLetras($this->total * 1.18),
                'observation' => $this->observation,
                'payment_method' => $this->payment_method,
                'payment_type' => $this->payment_type,
                'user_id' => Auth::id(),
            ]);
            ProductDetailServices::createDetailproductableExit($Sale, $this->products, $this->warehouse_id, 'Venta ID: ' . $Sale->id);
            UtilitisServices::generateAndAttachPdf(ModelsSale::class, $Sale);
            DB::commit();
            $this->successSwal('La venta se ha creado exitosamente.', type: 'session');
            $this->limpiar();
            return redirect()->route('admin.sales.index');
        } catch (\Exception $throwable) {
            DB::rollBack();
            Log::error('Error al crear la venta - Exception: ' . $throwable->getMessage());
            $this->errorSwal('Ha ocurrido un error inesperado al crear la venta. Por favor, inténtelo de nuevo.');
        } catch (\Throwable $throwable) {
            DB::rollBack();
            Log::error('Error al crear la venta - Throwable: ' . $throwable->getMessage());
            $this->errorSwal('Ha ocurrido un error inesperado al crear la venta. Por favor, inténtelo de nuevo.');
        }

        return null;
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

        return view('livewire.admin.create.sale', ['comprobante' => $comprobante, 'metodo_pago' => $metodo_pago, 'tipo_pago' => $tipo_pago]);
    }
}
