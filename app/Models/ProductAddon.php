<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAddon extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'addon_id',
        ];
    public function addon() {
        return $this->hasOne(Addon::class, 'id', 'addon_id')->select(['id','title'])->with('addonOptions');
    }
}