<?php

namespace App\Models;

class Identity extends BaseModel
{
    //
    protected $table = 'identities';

    protected $fillable = ['name', 'uuid'];
}
