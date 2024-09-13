<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Addon extends Model
{
    use HasFactory;
    protected $fillable = ['title','description','is_hidden'];
    
    public function addonOptions(){
        return $this->hasMany(AddonOption::class,'addon_id','id')->with('option');
    }
}