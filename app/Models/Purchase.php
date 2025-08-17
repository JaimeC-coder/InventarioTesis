<?php

namespace App\Models;

class Purchase extends BaseModel
{
    //
    protected $table = 'purchases';
    protected $fillable = [
        'voucher_type',
        'serie',
        'correlativo',
        'purchase_order_id',
        'date',
        'supplier_id',
        'warehouse_id',
        'total',
        'observation',
        'uuid',
    ];
}
