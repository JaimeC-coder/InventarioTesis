<?php

namespace App\Models;

class Transfer extends BaseModel
{
    protected $table = 'transfers';

    protected $fillable = [
        'type',
        'serie',
        'correlativo',
        'date',
        'total',
        'observaciones',
        'origin_warehouse_id',
        'destination_warehouse_id',
        'uuid',
    ];

    // Relación con almacenes de origen
    public function originWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'origin_warehouse_id');
    }

    // Relación con almacenes de destino
    public function destinationWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'destination_warehouse_id');
    }

    //Relación muchos a muchos polimórfica
    public function products()
    {
        return $this->morphToMany(Product::class, 'productable')
            ->using(Productable::class)
            ->withPivot('quantity', 'price', 'subtotal')
            ->withTimestamps();
    }

    //Relacion uno a muchos polimorfica
    public function inventories()
    {
        return $this->morphMany(Inventorie::class, 'inventoryable');
    }
}
