<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Review;
use App\Models\ProductColor;
use App\Models\ProductCategory;
use App\Models\ProductAddon;
use App\Models\AddonOption;
use App\Models\AddonOptionVariationItem;

class Product extends Model
{
    use HasFactory;
     protected $fillable = [
        'name',
        'short_desc',
        'long_desc',
        'image',
        'videos',
        'price',
        'shipping_desc',
        'is_kid_friendly',
        'width_min',
        'width_max',
        'height_min',
        'height_max',
        'measuring_protection_guarantee',
        'shipping',
        'in_stock',
        'stock_value',
        'warranty_options',
        'is_hidden'
    ];
    
    public function reviews() {
        return $this->hasMany(Review::class, 'product_id', 'id');
    }
    public function color() {
        return $this->hasMany(ProductColor::class, 'product_id', 'id')->with('color');
    }
    public function category() {
        return $this->hasMany(ProductCategory::class, 'product_id', 'id')->with('category');
    }
    public function addon() {
        return $this->hasMany(ProductAddon::class, 'product_id', 'id')->with('addon');
    }
    // Assuming ProductAddon has an addon_id foreign key to AddonOption
    // public function addonOptions() {
    //     return $this->hasManyThrough(
    //         AddonOption::class,
    //         ProductAddon::class,
    //         'product_id', // Foreign key on ProductAddon table
    //         'addon_id', // Foreign key on AddonOption table
    //         'id', // Local key on Product table
    //         'addon_id' // Local key on ProductAddon table
    //     );
    // }

    protected $casts = [
        'videos' => 'array', 
        'warranty_options' => 'array', 
    ];
}