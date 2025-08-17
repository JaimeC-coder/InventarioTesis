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
}
