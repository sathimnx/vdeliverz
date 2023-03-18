<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = [
        'shop_id', 'user_id', 'device_id', 'provider_id', 'car_provider_id'
    ];

}