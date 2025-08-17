<?php

namespace App\Models;

class Product extends BaseModel
{
    //
    protected $fillable = [
        'name',
        'sku',
        'barcode',
        'description',
        'price',
        'uuid',
        'category_id',
    ];
}
