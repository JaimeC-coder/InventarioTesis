<?php

namespace App\Models;

class Productable extends BaseModel
{
    protected $fillable = [
        'product_id',
        'quantity',
        'price',
        'subtotal',
        'productable_type',
        'productable_id',
        'uuid',
    ];
}
