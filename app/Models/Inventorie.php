<?php

namespace App\Models;

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
}
