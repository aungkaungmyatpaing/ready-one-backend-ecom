<?php

namespace App\Models;

use App\Casts\Color;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Currency;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id', 'id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }

    public function image()
    {
        return $this->hasOne(ProductImage::class, 'product_id', 'id')->latestOfMany();
    }

    public function scopeActive($query)
    {
        return $query->where('status', '1');
    }

    public function scopeInstock($query)
    {
        return $query->where('instock', '1');
    }

}
