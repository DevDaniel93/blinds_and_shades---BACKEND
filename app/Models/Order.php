<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
       protected $fillable = [
            'shipping_first_name',
            'shipping_last_name',
            'shipping_address',
            'shipping_apt',
            'shipping_city',
            'shipping_state',
            'shipping_zipcode',
            'billing_first_name',
            'is_billing_address_same',
            'billing_last_name',
            'billing_address',
            'billing_apt',
            'billing_city',
            'billing_state',
            'billing_zipcode',
            'phone',
            'email',
            'shipping_method',
            'shipping_cost',
            'payment_method_type',
            'payment_client_id',
            'payment_transection_id',
            'order_status',
            'discount_price',
            'total_price',
            'create_account',
            'user_id'
        ];
    
    public function orderItem(){
        return $this->hasMany(OrderItem::class,'order_id','id')->with('product');
    }
}

