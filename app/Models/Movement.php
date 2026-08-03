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
        'observation',
        'status',
        'subtotal',
        'igv',
        'total',
        'total_string',
        'user_id',
        'file_path',
        'warehouse_id',
        'reason_id',
        'uuid',
    ];

    // Relación con productos
    public function products()
    {
        return $this->morphToMany(Product::class, 'productable')
            ->using(Productable::class)
            ->withPivot('quantity', 'price', 'subtotal', 'price_type', 'product_name')
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

    // Relación con usuarios
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //Relacion uno a muchos polimorfica
    public function inventories()
    {
        return $this->morphMany(Inventorie::class, 'inventoryable');
    }
}
