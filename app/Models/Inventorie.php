<?php

namespace App\Models;

class Inventorie extends BaseModel
{
    //
    protected $table = 'inventories';

    protected $fillable = [
            'detail',
            'quantity_in',
            'quantity_out',
            'cost_in',
            'cost_out',
            'total_in',
            'total_out',
            'quantity_balance',
            'cost_balance',
            'total_balance',
            'product_id',
            'warehouse_id',
            'inventoryable_id',
            'inventoryable_type',
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
}
