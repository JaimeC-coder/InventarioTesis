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
        'status',
        'subtotal',
        'igv',
        'total',
        'total_string',
        'user_id',
        'observation',
        'file_path',
        'uuid',
    ];

    // Relación con proveedores
    public function supplier()
    {
        return $this->belongsTo(Supplier::class); //relacion uno a muchos inversa
    }

    // Relación con órdenes de compra
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class); //relacion uno a muchos
    }

    // Relación con usuarios
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //Relacion muchos a muchos polimorfica
    public function products()
    {
        return $this->morphToMany(Product::class, 'productable', 'productables', 'productable_id', 'product_id')
            ->using(Productable::class)
            ->withPivot('quantity', 'price', 'subtotal', 'price_type')
            ->withTimestamps();
    }
}
