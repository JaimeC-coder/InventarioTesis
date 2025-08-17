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
}
