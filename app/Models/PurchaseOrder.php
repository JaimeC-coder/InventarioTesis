<?php

namespace App\Models;

class PurchaseOrder extends BaseModel
{
    protected $table = 'purchase_orders';
    protected $fillable = [
        'voucher_type',
        'serie',
        'correlativo',
        'date',
        'supplier_id',
        'total',
        'observation',
        'uuid',
    ];
}
