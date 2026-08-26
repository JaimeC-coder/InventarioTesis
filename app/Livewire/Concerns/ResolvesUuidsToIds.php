<?php

namespace App\Livewire\Concerns;

use App\Models\Customer;
use App\Models\PurchaseOrder;
use App\Models\Quote;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Log;

/**
 * @property string|null $supplier_uuid
 * @property int|null    $supplier_id
 * @property string|null $product_uuid
 * @property int|null    $product_id
 * @property string|null $warehouse_uuid
 * @property int|null    $warehouse_id
 * @property string|null $purchase_order_uuid
 * @property int|null    $purchase_order_id
 * @property string|null $customer_uuid
 * @property int|null    $customer_id
 * @property string|null $quote_uuid
 * @property int|null    $quote_id
 * @property string|null $category_uuid
 * @property int|null    $category_id
 * @property int|null    $total
 */

trait ResolvesUuidsToIds
{
    protected function resolveSupplierId(): void
    {
        if (!empty($this->supplier_uuid)) {
            Log::info('Entro a suplier uuid: ' . $this->supplier_uuid);
            $this->supplier_id = Supplier::where('uuid', $this->supplier_uuid)->value('id');
            Log::info('Entro a suplier uuid: ' . $this->supplier_id);
        }
    }

    protected function resolveWarehouseId(): void
    {
        if (!empty($this->warehouse_uuid)) {
            Log::info('Entro a warehouse uuid: ' . $this->warehouse_uuid);
            $this->warehouse_id = Warehouse::where('uuid', $this->warehouse_uuid)->value('id');
        }
    }

    protected function resolvePurchaseOrderId(): void
    {
        if (!empty($this->purchase_order_uuid)) {
            Log::info('Entro a purchase order uuid: ' . $this->purchase_order_uuid);
            $this->purchase_order_id = PurchaseOrder::where('uuid', $this->purchase_order_uuid)->value('id');
        }
    }

    protected function resolveCustomerId(): void
    {
        if (!empty($this->customer_uuid)) {
            Log::info('Entro a customer uuid: ' . $this->customer_uuid);
            $this->customer_id = Customer::where('uuid', $this->customer_uuid)->value('id');
        }
    }

    protected function resolveQuoteId(): void
    {
        if (!empty($this->quote_uuid)) {
            Log::info('Entro a quote uuid: ' . $this->quote_uuid);
            $this->quote_id = Quote::where('uuid', $this->quote_uuid)->value('id');
        }
    }

    protected function resolveCategoryId(): void
    {
        if (!empty($this->category_uuid)) {
            Log::info('Entro a category uuid: ' . $this->category_uuid);
            $this->category_id = \App\Models\Category::where('uuid', $this->category_uuid)->value('id');
        }
    }

    protected function recalcularTotalDesdeProductos(): void
    {
        Log::info('Recalculando total desde productos');
        foreach ($this->products as $index => $producto) {
            $subtotal = round((float) $producto['price'] * (float) $producto['quantity'], 2);
            $this->products[$index]['subtotal'] = $subtotal;
        }

        $this->total = round(collect($this->products)->sum('subtotal'), 2);
    }
}
