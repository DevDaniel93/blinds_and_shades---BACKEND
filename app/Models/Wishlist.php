<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'product_id',
        'color_id',
        'mount_type',
        'height',
        'width',
        'guarantee_fit',
        'room_name',
        'room_wall',
        'customizations_selected',
        'warranty_options',
        'quantity',
    ];
    
    public function product() {
        return $this->hasOne(Product::class, 'id', 'product_id')->select(['id','name','image']);
    }
    
    protected $casts = [
        'warranty_options' => 'array',
        'customizations_selected' => 'array',
        ];
}