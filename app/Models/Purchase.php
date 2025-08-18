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
    //Relacion muchos a muchos polimorfica
    public function products()
    {
        return $this->morphToMany(Product::class, 'productable', 'productables', 'productable_id', 'product_id')
            ->withPivot('quantity', 'price', 'total')
            ->withTimestamps();
    }
}
