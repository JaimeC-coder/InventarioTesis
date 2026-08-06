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
        static::created(function (Inventorie $inventorie): void {
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
                ['quantity', 'product_name', 'warehouse_name', 'observation', 'uuid', 'inventory_id', 'updated_at']
            );
        });
        static::created(function (Inventorie $inventorie): void {
            $productStock = DB::table('products')->select('stock')->where('id', $inventorie->product_id)->first();
            if ($productStock) {
                $newStock = $inventorie->quantity_in > 0
                    ? $productStock->stock + $inventorie->quantity_in
                    : $productStock->stock - $inventorie->quantity_out;
                DB::table('products')->where('id', $inventorie->product_id)->update(['stock' => $newStock]);
            }
        });
    }
}
