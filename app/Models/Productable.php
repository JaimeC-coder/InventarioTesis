<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Support\Str;

class Productable extends MorphPivot
{
    protected $table = 'productables';

    protected $fillable = [
        'product_id',
        'quantity',
        'price',
        'subtotal',
        'productable_type',
        'productable_id',
        'uuid',
    ];

    protected static function booted()
    {
        static::creating(function ($model): void {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}
