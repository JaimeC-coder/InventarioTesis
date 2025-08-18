<?php

namespace App\Models;

class Customer extends BaseModel
{
    //
    protected $table = 'customers';
    protected $fillable = [
                    'document_number',
                    'identity_id',
                    'name',
                    'email',
                    'phone',
                    'address',
                    'uuid',
                ];
    //relacion con identidad
    public function identity()
    {
        return $this->belongsTo(Identity::class); //relacion uno a muchos
    }
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
}
