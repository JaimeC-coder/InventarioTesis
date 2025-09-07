<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Measure extends BaseModel
{
    protected $table = 'measures';

    protected $fillable = [
        'name',
        'abbreviation',
        'uuid',
    ];


    // Relación con productos
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
