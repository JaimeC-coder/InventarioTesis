<?php

namespace App\Models;

class Supplier extends BaseModel
{
    protected $table = 'suppliers';
    protected $fillable = [
        'identity_id',
        'document_number',
        'name',
        'email',
        'phone',
        'address',
        'uuid',
    ];
}
