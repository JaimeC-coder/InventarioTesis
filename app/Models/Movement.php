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
        'product_id',
        'warehouse_id',
        'movementable_id',
        'movementable_type',
        'uuid',
    ];
}
