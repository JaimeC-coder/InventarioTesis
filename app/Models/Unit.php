<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends BaseModel
{
    protected $table = 'units';

    protected $fillable = [
        'name',
        'uuid',
    ];
}
