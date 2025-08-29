<?php

namespace App\Models;

class Movement extends BaseModel
{
    protected $table = 'movements';

    protected $fillable = [
        'type',
        'serie',
        'correlativo',
        'date',
        'observaciones',
        'total',
        'warehouse_id',
        'reason_id',
        'uuid',
    ];

    // Relación con productos
    public function products()
    {
        return $this->morphToMany(Product::class, 'productable')
            ->using(Productable::class)
            ->withPivot('quantity', 'price', 'subtotal')
            ->withTimestamps();
    }

    // Relación con almacenes
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    // Relación con motivos
    public function reason()
    {
        return $this->belongsTo(Reason::class);
    }
}
