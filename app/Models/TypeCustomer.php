<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeCustomer extends BaseModel
{
    protected $table = 'type_customers';

    protected $fillable = [
        'type',
        'porcentage_discount',
        'uuid',
    ];

    //relacion con clientes
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
