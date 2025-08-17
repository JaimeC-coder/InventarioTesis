<?php

namespace App\Models;

class Reason extends BaseModel
{
    protected $table = 'reasons';

    protected $fillable = [
        'name',
        'uuid',
    ];
}
