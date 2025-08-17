<?php

namespace App\Models;

class Quote extends BaseModel
{
    protected $table = 'quotes';
    protected $fillable = [
        'voucher_type',
        'serie',
        'correlativo',
        'date',
        'total',
        'observation',
        'customer_id',
        'uuid',
    ];
}
