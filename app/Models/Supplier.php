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
    public function identity()
    {
        return $this->belongsTo(Identity::class); //relacion uno a muchos
    }

    // Relacion con compras
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
