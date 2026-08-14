<?php

namespace App\Observers;

use App\Enum\PurchasesStatusEnum;
use App\Exceptions\PurchaseStatusLockedException;
use App\Models\Purchase;
use App\Services\ProductDetailServices;
use Illuminate\Support\Facades\Log;

class PurchaseObserver
{
    /**
     * Handle the Purchase "created" event.
     */
    public function created(Purchase $purchase): void
    {
        //
    }

    public function updating(Purchase $purchase): void
    {

        if (
            $purchase->getOriginal('status') === PurchasesStatusEnum::RECIBIDO->value
            && $purchase->isDirty('status')
        ) {
            throw new PurchaseStatusLockedException();
        }
    }

    /**
     * Handle the Purchase "updated" event.
     */
    public function updated(Purchase $purchase): void
    {
        if ($purchase->wasChanged('status') && $purchase->status === PurchasesStatusEnum::RECIBIDO->value) {
            Log::info('Purchase status changed to RECIBIDO for purchase ID: ' . $purchase->id);
            ProductDetailServices::updateKardexForProduct($purchase, $purchase->products->toArray(), $purchase->warehouse_id, 'entry', 'Compra recibida');
        }
    }

    /**
     * Handle the Purchase "deleted" event.
     */
    public function deleted(Purchase $purchase): void
    {
        //
    }

    /**
     * Handle the Purchase "restored" event.
     */
    public function restored(Purchase $purchase): void
    {
        //
    }

    /**
     * Handle the Purchase "force deleted" event.
     */
    public function forceDeleted(Purchase $purchase): void
    {
        //
    }
}
