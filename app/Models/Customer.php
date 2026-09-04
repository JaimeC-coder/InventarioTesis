<?php

namespace App\Models;

use App\Enum\DocumentEnum;

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

    public function getIdentityTypeLabelAttribute()
    {
        return DocumentEnum::tryFrom(trim($this->identity))?->label() ?? '';
    }
}
