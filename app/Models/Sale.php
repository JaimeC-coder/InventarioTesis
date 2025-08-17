<?php

namespace App\Models;

class Sale extends BaseModel
{
    protected $table = 'sales';

    protected $fillable = [
        'voucher_type',
        'serie',
        'correlativo',
        'date',
        'quote_id',
        'customer_id',
        'warehouse_id',
        'total',
        'observation',
        'uuid',
    ];
}
