<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable =[
        
        'title',
        'description',
        'image',
        'parent_id',
        'is_hidden',

        ];
    
}