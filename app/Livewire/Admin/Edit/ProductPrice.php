<?php

namespace App\Livewire\Admin\Edit;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ProductPrice extends Component
{
    public $productuuid;

    public $name;

    public $price_sale_regular;

    public $price_sale_a1;

    public $price_purchase;

    public $showModal = false;

    public $price_sale_a1_final;

    public $price_sale_regular_final;

    #[\Livewire\Attributes\On('updateprice')]
    public function loadProduct($productuuid): void
    {
        $product = Product::where('uuid', $productuuid)->first();
        if ($product) {
            $this->productuuid = $product->uuid;
            $this->name = $product->name;
            $this->price_sale_regular = $product->price_sale_regular;
            $this->price_sale_a1 = $product->price_sale_a1;
            $this->price_purchase = $product->price_purchase;
            $this->showModal = true;
        }
    }

    public function save(): void
    {
        $this->validate([
            'price_sale_regular' => 'nullable|numeric|min:1',
            'price_sale_a1' => 'nullable|numeric|min:1',
            'price_purchase' => 'nullable|numeric|min:1',
        ]);
        DB::beginTransaction();
        try {
            $product = Product::where('uuid', $this->productuuid)->first();
            if ($product) {
                $product->update([
                    'price_sale_regular' => $this->price_sale_regular,
                    'price_sale_a1' => $this->price_sale_a1,
                    'price_purchase' => $this->price_purchase,
                ]);
                DB::commit();
                $this->showModal = false;
                $this->dispatch('pg:eventRefresh-product-table-itbilq-table'); // refresca tabla PowerGrid
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar precio del producto: ' . $e->getMessage());
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Ocurrió un error al actualizar el precio del producto.',
            ]);
            $this->showModal = false;
        }
    }

    public function editPrice(): void
    {
        // 1.18;
        //si corrijo el numero osea borro el numero quiero que se cambie a 0
        if (empty($this->price_sale_a1_final)) {
            $this->price_sale_a1_final = 0;
        }

        if (empty($this->price_sale_regular_final)) {
            $this->price_sale_regular_final = 0;
        }

        $this->price_sale_a1 = round($this->price_sale_a1_final / 1.18, 6);
        $this->price_sale_regular = round($this->price_sale_regular_final / 1.18, 6);
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.edit.product-price');
    }
}
