<?php

namespace App\Models;

class Warehouse extends BaseModel
{
    protected $table = 'warehouses';
    protected $fillable = [
        'name',
        'location',
        'uuid',
    ];
}
