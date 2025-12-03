<?php

namespace App\Models;

class Quote extends BaseModel
{
    protected $table = 'quotes';

    protected $fillable = [
        'voucher_type',
        'serie',
        'correlativo',
        'date',
        'total',
        'observation',
        'customer_id',
        'uuid',
    ];

    // Relación con clientes
    public function customer()
    {
        return $this->belongsTo(Customer::class); //relacion uno a muchos inversa
    }

    // Relación con ventas
    public function sales()
    {
        return $this->hasMany(Sale::class);
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
