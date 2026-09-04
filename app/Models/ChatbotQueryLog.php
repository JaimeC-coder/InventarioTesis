<?php

namespace App\Models;

class ChatbotQueryLog extends BaseModel
{
    protected $fillable = [
        'user_id',
        'entity',
        'metric',
        'filters',
        'uuid',
    ];

    protected $casts = [
        'filters' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
