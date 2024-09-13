<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddonOption extends Model
{
    use HasFactory;
    protected $fillable = [
        'addon_id',
        'option_id',
        ];
    public function option(){
        return $this->hasOne(Option::class,'id','option_id');
    }
}