<?php

namespace App\Models;

class Movement extends BaseModel
{
    protected $table = 'movements';
    protected $fillable = [
                    'type',
                    'quantity',
                    'cost',
                    'total',
                    'warehouse_id',
                    'movementable_id',
                    'movementable_type',
                    'uuid',
                ];
    // Relación polimórfica
    public function movementable()
    {
        return $this->morphTo();
    }
    // Relación con productos
    public function products()
    {
        return $this->morphToMany(Product::class, 'productable')
            ->withPivot('quantity', 'price', 'subtotal')
            ->withTimestamps();
    }
    // Relación con almacenes
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
