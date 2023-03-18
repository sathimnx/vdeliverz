<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = [
        'coupon_code', 'active', 'max_order_amount', 'coupon_description', 'symbol', 'expired_on','min_order_amt','Discount_use_amt'
    ];
}
