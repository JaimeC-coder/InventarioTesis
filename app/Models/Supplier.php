<?php

namespace App\Models;

use App\Enum\DocumentEnum;

class Supplier extends BaseModel
{
    protected $table = 'suppliers';

    protected $fillable = [
        'identity',
        'document_number',
        'name',
        'email',
        'phone',
        'address',
        'uuid',
    ];

    // Relación con órdenes de compra
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    // Relacion con compras
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function getIdentityTypeLabelAttribute()
    {
        return DocumentEnum::tryFrom(trim($this->identity))?->label() ?? '';
    }
}
