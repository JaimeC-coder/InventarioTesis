<?php

namespace App\Models;

class Category extends BaseModel
{
    //
    protected $table = 'categories';

    protected $fillable = [
            'name',
            'description',
            'uuid',
        ];

    //relacion con productos
    public function products()
    {
        return $this->hasMany(Product::class);//relacion uno a muchos
    }
}
