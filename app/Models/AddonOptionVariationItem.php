<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddonOptionVariationItem extends Model
{
    use HasFactory;
    protected $fillable =[
     			   
        'addon_option_id',
        'title',
        'price',
        'is_hidden',

        ];
    
}