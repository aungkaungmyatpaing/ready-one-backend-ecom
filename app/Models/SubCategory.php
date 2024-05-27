<?php

namespace App\Models;

use App\Casts\Image;
use App\Http\Controllers\API\ProductController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;
    protected $casts = [
        'image' => Image::class,
    ];
    public function category(){
       return $this->belongsTo(Category::class,'category_id');
    }
    public function product(){
        return $this->hasMany(Product::class,'sub_category_id','id');
    }
}
