<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;
    protected $fillable = [
        'addon_id',
        'title',
        'description',
        'video_link',
        'is_paid',
        'amount',
        'image',
        'type',
        'variables',
        'is_kid_friendly',
        'is_hidden'
        ];
        
    protected $casts = [
        'variables' => 'array', 
    ];
        
}