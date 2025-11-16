<?php

namespace App\Models;

class Unit extends BaseModel
{
    protected $table = 'units';

    protected $fillable = [
        'name',
        'code',
        'uuid',
    ];

    //relacion con productos
    public function products()
    {
        return $this->hasMany(Product::class);//relacion uno a muchos
    }
}
