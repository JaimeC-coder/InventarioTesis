<?php

namespace App\Models;

class Unit extends BaseModel
{
    protected $table = 'units';

    protected $fillable = [
        'name',
        'uuid',
    ];
}
