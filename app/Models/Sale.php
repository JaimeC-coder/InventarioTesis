<?php

namespace App\Models;

class Sale extends BaseModel
{
    protected $table = 'sales';

    protected $fillable = [
        'voucher_type',
        'serie',
        'correlativo',
        'date',
        'quote_id',
        'customer_id',
        'warehouse_id',
        'total',
        'observation',
        'uuid',
    ];

    // Relación con cotizaciones
    public function quote()
    {
        return $this->belongsTo(Quote::class); //relacion uno a muchos inversa
    }

    // Relación con clientes
    public function customer()
    {
        return $this->belongsTo(Customer::class); //relacion uno a muchos inversa
    }

    // Relación con almacenes
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class); //relacion uno a muchos inversa
    }

      //Relacion muchos a muchos polimorfica
    public function products()
    {
        return $this->morphToMany(Product::class, 'productable', 'productables', 'productable_id', 'product_id')
            ->withPivot('quantity', 'price', 'total')
            ->withTimestamps();
    }
}
