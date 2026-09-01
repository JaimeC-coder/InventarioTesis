<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Product extends BaseModel
{
    protected $fillable = [
        'name',
        'code',
        'category_code',
        'barcode',
        'description',
        'price_sale_regular',
        'price_sale_a1',
        'price_purchase',
        'stock',
        'min_stock',
        'is_active_product',
        'product_base_id',
        'uuid',
        'category_id',
        'stock',
        'supplier_id',
        'unit_id',
        'measure_id',
    ];

    //Accesores

    public function image(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->images->count() > 0 ? Storage::url($this->images->first()->path) : 'https://media.istockphoto.com/id/1409329028/vector/no-picture-available-placeholder-thumbnail-icon-illustration-design.jpg?s=612x612&w=0&k=20&c=_zOuJu755g2eEUioiOUdz_mHKJQJn-tDgIAhQzyeKUQ='
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

    // Relacion con unidad
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // Relacion con medida
    public function measure()
    {
        return $this->belongsTo(Measure::class);
    }

    // Relacion con productos base
    public function productBase()
    {
        return $this->belongsTo(Product::class, 'product_base_id');
    }

    // Relacion con proveedores
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Relacion inversa con Record para obtener el stock por almacén
    public function records()
    {
        return $this->hasMany(Record::class, 'product_id');
    }

    public function stockByWarehouse()
    {
        return $this->hasMany(Record::class, 'product_id')
            ->with('warehouse');
    }
}
