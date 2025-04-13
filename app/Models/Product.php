<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'slug', 'description', 'price', 'stock_quantity', 'is_active'];

    // Define the relationship with categories
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }

    // Define the relationship with product images
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
}
