<?php

namespace App\Models;

class Measure extends BaseModel
{
    protected $table = 'measures';

    protected $fillable = [
        'name',
        'abbreviation',
        'code',
        'description_for_product',
        'uuid',
    ];

    // Relación con productos
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
