<?php

namespace App\Models;

class Transfer extends BaseModel
{
    protected $table = 'transfers';
    protected $fillable = [
        'type',
        'serie',
        'correlativo',
        'date',
        'total',
        'observaciones',
        'origin_warehouse_id',
        'destination_warehouse_id',
        'uuid',
    ];
}
