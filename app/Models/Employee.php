<?php

namespace App\Models;

class Employee extends BaseModel
{
    protected $fillable = [
        'document',
        'phone',
        'address',
        'fechaNacimiento',
        'user_id',
    ];

    protected $casts = [
        'fechaNacimiento' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
