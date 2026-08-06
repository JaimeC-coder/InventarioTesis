<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class Inventorie extends BaseModel
{
    protected $table = 'inventories';

    protected $fillable = [
        'detail',
        'quantity_in',
        'quantity_out',
        'quantity_total',
        'product_name',
        'product_id',
        'warehouse_id',
        'type', //Entrada ,Salida ,Traslado-IGS , Traslado-IGD
        //traslado-code por verse
        'uuid',
    ];

    // Relación polimórfica
    public function inventoryable()
    {
        return $this->morphTo();
    }

    //relacion con productos
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    //relacion con almacenes
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    // En Inventorie.php
    protected static function booted()
    {
        static::created(function (Inventorie $inventory) {
            DB::table('records')->upsert(
                [
                    [
                        'product_id' => $inventory->product_id,
                        'product_name' => $inventory->product_name,
                        'product_code' => $inventory->product_code ?? $inventory->product?->code ?? '',
                        'warehouse_id' => $inventory->warehouse_id,
                        'warehouse_name' => $inventory->warehouse->name,
                        'quantity' => $inventory->quantity_total,
                        'observation' => $inventory->detail,
                        'uuid' => $inventory->uuid,
                        'inventory_id' => $inventory->id,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                ],
                ['product_id', 'warehouse_id'],
                ['quantity', 'product_name', 'warehouse_name', 'observation', 'uuid', 'inventory_id', 'updated_at']
            );
        });

        static::created(function (Inventorie $inventory) {
            $productStock = DB::table('products')->select('stock')->where('id', $inventory->product_id)->first();

            if ($productStock) {
                $newStock = $inventory->quantity_in > 0
                    ? $productStock->stock + $inventory->quantity_in
                    :  $productStock->stock - $inventory->quantity_out;

                DB::table('products')->where('id', $inventory->product_id)->update(['stock' => $newStock]);
            }
        });
    }
}
