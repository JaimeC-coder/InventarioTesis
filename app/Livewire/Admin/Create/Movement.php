<?php

namespace App\Livewire\Admin\Create;

use App\Models\Inventorie;
use App\Models\Movement as ModelsMovement;
use App\Models\Product;
use App\Models\Reason;
use App\Models\Warehouse;
use App\Services\FileServices;
use App\Traits\HandlesSwalMessagesTrait;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Movement extends Component
{
    use HandlesSwalMessagesTrait;

    public $type = 1;

    public $serie = 'MV01';

    public $correlativo = 1;

    public $date = '';

    public $observation = '';

    public $total = 0.00;

    public $reason_id = '';

    public $reason_uuid = '';

    public $warehouse_id = '';

    public $warehouse_uuid = '';

    public $product_uuid = '';

    public ?int $product_id ;

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

    public function updated($property, $value): void
    {
        if ($property === 'type') {
            $this->reset('reason_uuid');
        }
    }

    public function mount(): void
    {
        $this->correlativo = ModelsMovement::where('serie', $this->serie)->max('correlativo') + 1;
        $this->date = now()->format('Y-m-d');
    }

    public function addProduct(): void
    {
        $this->validate([
            'product_uuid' => 'required|exists:products,uuid',
            'warehouse_uuid' => 'required|exists:warehouses,uuid',
        ]);
        $product = Product::where('uuid', $this->product_uuid)->first();
        $exists = collect($this->products)->where('id', $product->id)->first();
        $lastrecortd = Inventorie::where('product_id', $product->id)
            ->where('warehouse_id', $this->warehouse_id)
            ->latest()
            ->first();
        $costBalance = $lastrecortd ? $lastrecortd->cost_balance : 0;
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
            'price' => $costBalance,
            'subtotal' => $costBalance,
        ];
        $this->reset('product_uuid');
    }

    public function save()
    {
        if (!empty($this->warehouse_uuid)) {
            $this->warehouse_id = Warehouse::where('uuid', $this->warehouse_uuid)->value('id');
        }

        if (!empty($this->reason_uuid)) {
            $this->reason_id = Reason::where('uuid', $this->reason_uuid)->value('id');
        }

        $this->validate([
            'type' => 'required|in:1,2',
            'serie' => 'required|string|max:20',
            'correlativo' => 'required|integer|min:1',
            'date' => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'reason_id' => 'nullable|exists:reasons,id',
            'total' => 'required|numeric|min:0.01',
            'observation' => 'nullable|string|max:500',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
        ], [], [
            'type' => 'Tipo de movimiento',
            'serie' => 'Serie',
            'correlativo' => 'Correlativo',
            'date' => 'Fecha',
            'warehouse_id' => 'Almacén',
            'total' => 'Total',
            'observation' => 'observation',
            'reason_id' => 'ID del motivo',
            'products.*.id' => 'ID del producto',
            'products.*.quantity' => 'Cantidad del producto',
            'products.*.price' => 'Precio del producto',
        ]);
        //quiero que esto se tenga en una transacción
        $Movement = ModelsMovement::create([
            'type' => $this->type,
            'serie' => $this->serie,
            'correlativo' => $this->correlativo,
            'date' => $this->date,
            'warehouse_id' => $this->warehouse_id,
            'subtotal' => $this->total,
            'igv' => $this->total * 0.18,
            'total' => $this->total * 1.18,
            'total_string' => $this->totalEnLetras($this->total * 1.18),
            'observation' => $this->observation,
            'user_id' => Auth::id(),
            'reason_id' => $this->reason_id,
        ]);
        foreach ($this->products as $product) {
            $product_id = Product::where('id', $product['id'])->value('id');
            $Movement->products()->attach($product_id, [
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
            if ($this->type == 1) {
                $newQuantity = $lastQuantity + $product['quantity'];
                $newTotal = $lastTotal + ($product['quantity'] * $product['price']);
                $costBalance = $newTotal / $newQuantity;
                $Movement->inventories()->create([
                    'detail' => 'Movimiento de ' . ($this->type == 1 ? 'entrada' : 'salida'),
                    'quantity_in' => $product['quantity'],
                    'cost_in' => $product['price'],
                    'total_in' => $product['quantity'] * $product['price'],
                    'warehouse_id' => $this->warehouse_id,
                    'product_id' => $product_id,
                    'quantity_balance' => $newQuantity,
                    'total_balance' => $newTotal,
                    'cost_balance' => $costBalance,
                ]);
            } elseif ($this->type == 2) {
                $newQuantity = $lastQuantity - $product['quantity'];
                $newTotal = $lastTotal - ($product['quantity'] * $product['price']);
                $costBalance = $newTotal / ($newQuantity ?: 1);
                $Movement->inventories()->create([
                    'detail' => 'Movimiento de ' . ($this->type == 1 ? 'entrada' : 'salida'),
                    'quantity_out' => $product['quantity'],
                    'cost_out' => $product['price'],
                    'total_out' => $product['quantity'] * $product['price'],
                    'warehouse_id' => $this->warehouse_id,
                    'product_id' => $product_id,
                    'quantity_balance' => $newQuantity,
                    'total_balance' => $newTotal,
                    'cost_balance' => $costBalance,
                ]);
            }
        }

        $fileDirection = FileServices::generatePdfNow(['model' => ModelsMovement::class, 'uuids' => $Movement->uuid]);
        $Movement->update(['file_path' => $fileDirection]);
        $Movement->save();
        $this->successSwal('El movimiento se ha creado exitosamente.', type: 'session');

        return redirect()->route('admin.movements.index');
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
        return view('livewire.admin.create.movement');
    }
}
