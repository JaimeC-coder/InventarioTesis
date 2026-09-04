<?php

namespace App\Models;

class Record extends BaseModel
{
    protected $table = 'records';

    protected $fillable = [
        'warehouse_id',
        'warehouse_name',
        'quantity',
        'product_id',
        'product_name',
        'product_code',
        'observation',
        'recordable_id',
        'recordable_type',
        'uuid',
    ];

    // Relación polimórfica
    public function recordable()
    {
        return $this->morphTo();
    }

    // Relación con productos
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relación con almacenes
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
