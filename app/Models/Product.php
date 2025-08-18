<?php

namespace App\Models;

class Product extends BaseModel
{
    //
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
    //Relacion polimorfica con imagenes
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
