<?php

namespace App\Models;

class Purchase extends BaseModel
{
    protected $table = 'purchases';

    protected $fillable = [
        'voucher_type',
        'serie',
        'correlativo',
        'purchase_order_id',
        'date',
        'supplier_id',
        'warehouse_id',
        'status',
        'subtotal',
        'igv',
        'total',
        'total_string',
        'user_id',
        'observation',
        'currency',
        'payment_method',
        'payment_type',
        'file_path',
        'uuid',
    ];

    // Relación con proveedores
    public function supplier()
    {
        return $this->belongsTo(Supplier::class); //relacion uno a muchos inversa
    }

    // Relación con almacenes
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class); //relacion uno a muchos inversa
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class); //relacion uno a muchos inversa
    }

    // Relación con usuarios
    public function user()
    {
        return $this->belongsTo(User::class); //relacion uno a muchos inversa
    }

    //Relacion muchos a muchos polimorfica
    public function products()
    {
        return $this->morphToMany(Product::class, 'productable', 'productables', 'productable_id', 'product_id')
            ->using(Productable::class)
            ->withPivot('quantity', 'price', 'subtotal', 'price_type');
    }

    //Relacion uno a muchos polimorfica
    public function inventories()
    {
        return $this->morphMany(Inventorie::class, 'inventoryable');
    }
}
