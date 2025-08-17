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

    //Relacion muchos a muchos polimorfica
    public function products()
    {
        return $this->morphToMany(Product::class, 'productable', 'productables', 'productable_id', 'product_id')
            ->withPivot('quantity', 'price', 'total')
            ->withTimestamps();
    }
}
