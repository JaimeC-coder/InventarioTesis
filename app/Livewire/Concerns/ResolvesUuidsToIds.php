<?php

namespace App\Livewire\Concerns;

use App\Models\Customer;
use App\Models\PurchaseOrder;
use App\Models\Quote;
use App\Models\Supplier;
use App\Models\Warehouse;

trait ResolvesUuidsToIds
{
    protected ?int $supplier_id = null;

    protected ?int $warehouse_id = null;

    protected ?int $purchase_order_id = null;

    protected ?int $customer_id = null;

    protected ?int $quote_id = null;

    protected function resolveSupplierId(): void
    {
        if (!empty($this->supplier_uuid)) {
            $this->supplier_id = Supplier::where('uuid', $this->supplier_uuid)->value('id');
        }
    }

    protected function resolveWarehouseId(): void
    {
        if (!empty($this->warehouse_uuid)) {
            $this->warehouse_id = Warehouse::where('uuid', $this->warehouse_uuid)->value('id');
        }
    }

    protected function resolvePurchaseOrderId(): void
    {
        if (!empty($this->purchase_order_uuid)) {
            $this->purchase_order_id = PurchaseOrder::where('uuid', $this->purchase_order_uuid)->value('id');
        }
    }

    protected function resolveCustomerId(): void
    {
        if (!empty($this->customer_uuid)) {
            $this->customer_id = Customer::where('uuid', $this->customer_uuid)->value('id');
        }
    }

    protected function resolveQuoteId(): void
    {
        if (!empty($this->quote_uuid)) {
            $this->quote_id = Quote::where('uuid', $this->quote_uuid)->value('id');
        }
    }
}
