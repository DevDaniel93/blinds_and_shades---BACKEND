<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory;
     protected $fillable =[
        	
        'title',
        'primary_image',
        'variations',
        'is_hidden',

        ];
    protected $casts = [
        'variations' => 'array', 
    ];
}