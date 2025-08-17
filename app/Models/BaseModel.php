<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BaseModel extends Model
{
    //
    use SoftDeletes;
    protected $keyType = 'int';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
                'uuid' => 'uuid',
            ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted()
    {
        static::creating(function ($model): void {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}
