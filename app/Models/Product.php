<?php

namespace App\Models;

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
