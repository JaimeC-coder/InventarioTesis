<?php

namespace App\Observers;

use App\Models\Inventorie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventorieObserver
{
    /**
     * Handle the Inventorie "created" event.
     */
    public function created(Inventorie $inventorie): void
    {
        //
        Log::info('Registro de inventario de tipo: ' . $inventorie->type . ' creado para el producto: ' . $inventorie->product_name . ' en el almacén: ' . $inventorie->warehouse->name);

        if (in_array($inventorie->type, ['ENTRADA', 'TRASLADO-IGD'], true)) {
            DB::table('products')->where('id', $inventorie->product_id)->increment('stock', $inventorie->quantity_in);
        } else {
            DB::table('products')->where('id', $inventorie->product_id)->decrement('stock', $inventorie->quantity_out);
        }

        // La clave de conflicto debe ser la restricción única real de la tabla
        // (product_id, warehouse_id). Usar 'inventory_id' (siempre distinto en
        // cada fila nueva) hace que en SQL Server el MERGE nunca encuentre
        // coincidencia y siempre intente INSERT, chocando con esa unique key
        // en cuanto un producto recibe un segundo movimiento en el mismo almacén.
        DB::table('records')->upsert(
            [
                [
                    'product_id' => $inventorie->product_id,
                    'product_name' => $inventorie->product_name,
                    'product_code' => $inventorie->product_code ?? $inventorie->product?->code ?? '',
                    'warehouse_id' => $inventorie->warehouse_id,
                    'warehouse_name' => $inventorie->warehouse->name,
                    'quantity' => $inventorie->quantity_total,
                    'observation' => $inventorie->detail,
                    'uuid' => $inventorie->uuid,
                    'inventory_id' => $inventorie->id,
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            ],
            ['product_id', 'warehouse_id'],
            ['product_name', 'product_code', 'warehouse_name', 'quantity', 'observation', 'uuid', 'inventory_id', 'updated_at']
        );
    }

    /**
     * Handle the Inventorie "updated" event.
     */
    public function updated(Inventorie $inventorie): void
    {
        //
    }

    /**
     * Handle the Inventorie "deleted" event.
     */
    public function deleted(Inventorie $inventorie): void
    {
        //
    }

    /**
     * Handle the Inventorie "restored" event.
     */
    public function restored(Inventorie $inventorie): void
    {
        //
    }

    /**
     * Handle the Inventorie "force deleted" event.
     */
    public function forceDeleted(Inventorie $inventorie): void
    {
        //
    }
}
