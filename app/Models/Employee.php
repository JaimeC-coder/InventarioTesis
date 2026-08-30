<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
