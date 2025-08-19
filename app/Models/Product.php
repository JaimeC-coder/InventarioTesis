<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Product extends BaseModel
{
    protected $fillable = [
        'name',
        'sku',
        'barcode',
        'description',
        'price',
        'uuid',
        'category_id',
    ];

    //Accesores

    public function image() : Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->images->count() > 0 ? Storage::url($this->images->first()->path) : 'https://media.istockphoto.com/id/1409329028/vector/no-picture-available-placeholder-thumbnail-icon-illustration-design.jpg?s=612x612&w=0&k=20&c=_zOuJu755g2eEUioiOUdz_mHKJQJn-tDgIAhQzyeKUQ='
        );
    }

    // Relación con categorías
    public function category()
    {
        return $this->belongsTo(Category::class); // relacion uno a muchos
    }

    // Relación con inventarios
    public function inventories()
    {
        return $this->hasMany(Inventorie::class);
    }


    // Relacion mucho a muchos polimorfica con purchase
    public function purchases()
    {
        return $this->morphedByMany(Purchase::class, 'productable');
    }
    // Relacion mucho a muchos polimorfica con quotes
    public function quotes()
    {
        return $this->morphedByMany(Quote::class, 'productable');
    }

    //Relacion polimorfica con imagenes
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
