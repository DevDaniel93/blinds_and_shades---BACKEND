<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'first_name',
        'last_name',
        'phone',
        'image',
        'user_role',
        'password',
        'billing_first_name',	
        'billing_last_name',	
        'billing_street_address',	
        'billing_apt',	
        'billing_city',	
        'billing_state',	
        'billing_zip_code',	
        'billing_country',	
        'shipping_first_name',	
        'shipping_last_name',	
        'shipping_street_address',	
        'shipping_apt',	
        'shipping_city', 
        'shipping_state', 
        'shipping_zip_code', 
        'shipping_country', 
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed'
    ];
}
