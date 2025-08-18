<?php

namespace App\Models;

class Warehouse extends BaseModel
{
    protected $table = 'warehouses';

    protected $fillable = [
        'name',
        'location',
        'uuid',
    ];

    // Relación con transferencias (almacenes de origen)
    public function originTransfers()
    {
        return $this->hasMany(Transfer::class, 'origin_warehouse_id');
    }

    // Relación con transferencias (almacenes de destino)
    public function destinationTransfers()
    {
        return $this->hasMany(Transfer::class, 'destination_warehouse_id');
    }
}
