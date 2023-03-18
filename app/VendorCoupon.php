<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VendorCoupon extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = [
        'coupon_code', 'active', 'max_order_amount', 'coupon_description', 'symbol', 'expired_on','min_order_amt','Discount_use_amt'
    ];
    
     public function subCategory()
    {
        return $this->belongsTo('App\SubCategory');
    }
    
      public function shop()
    {
        return $this->belongsTo('App\Shop');
    }
    
     public function products()
    {
        return $this->belongsTo('App\Product');
    }
      public function Category()
    {
        return $this->belongsTo('App\Category');
    }
}
