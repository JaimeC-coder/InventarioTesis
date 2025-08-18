<?php

namespace App\Models;

class Image extends BaseModel
{
    protected $table = 'images';

    protected $fillable = [
        'path',
        'size',
        'imageable_id',
        'imageable_type',
        'alt_text',
        'uuid',
    ];

    // Relación polimórfica
    public function imageable()
    {
        return $this->morphTo(); // Relación polimórfica
    }
}
