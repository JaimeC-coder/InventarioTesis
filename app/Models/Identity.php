<?php

namespace App\Models;

class Identity extends BaseModel
{
    //
    protected $table = 'identities';

    protected $fillable = ['name', 'uuid'];

    //relacion con clientes
    public function customers()
    {
        return $this->hasMany(Customer::class); //relacion uno a muchos
    }

    //relacion con suppliers
    public function suppliers()
    {
        return $this->hasMany(Supplier::class); //relacion uno a muchos
    }
}
