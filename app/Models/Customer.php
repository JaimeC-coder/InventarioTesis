<?php

namespace App\Models;

class Customer extends BaseModel
{
    protected $table = 'customers';

    protected $fillable = [
        'document_number',
        'identity',
        'name',
        'email',
        'phone',
        'address',
        'type',
        'uuid',
    ];

    // Relación con ventas
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    // Relación con cotizaciones
    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    // //relacion con tipo de cliente
    // public function typeCustomer()
    // {
    //     return $this->belongsTo(TypeCustomer::class); //relacion uno a muchos inversa
    // }
}
