<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'price',
        'promo_price', 'image', 'images', 'stock', 'unit',
        'is_available', 'is_featured', 'is_halal', 'is_made_in_morocco'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'promo_price' => 'decimal:2',
        'images' => 'array',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'is_halal' => 'boolean',
        'is_made_in_morocco' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getCurrentPrice()
    {
        return $this->promo_price ?? $this->price;
    }

    public function hasPromo()
    {
        return !is_null($this->promo_price) && $this->promo_price < $this->price;
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->where('stock', '>', 0);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
