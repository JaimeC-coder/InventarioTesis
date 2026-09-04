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
        'status',
        'subtotal',
        'igv',
        'total',
        'total_string',
        'user_id',
        'observation',
        'origin_warehouse_id',
        'destination_warehouse_id',
        'file_path',
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
            ->withPivot('quantity', 'price', 'subtotal', 'price_type', 'product_name')
            ->withTimestamps();
    }

    //Relacion uno a muchos polimorfica
    public function records()
    {
        return $this->morphMany(Record::class, 'inventoryable');
    }
}
