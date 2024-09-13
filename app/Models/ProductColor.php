<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductColor extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'color_id',
        ];
        
    public function color() {
        return $this->hasOne(Color::class, 'id', 'color_id')->select(['id','title','primary_image','variations']);
    }
    
}