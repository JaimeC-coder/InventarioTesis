<?php

namespace App\Models;

class Supplier extends BaseModel
{
    protected $table = 'suppliers';

    protected $fillable = [
        'identity_id',
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

    // Relación con identidades
    public function identities()
    {
        return $this->hasMany(Identity::class);
    }

    // Relacion con compras
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
