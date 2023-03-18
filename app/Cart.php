<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = [
        'user_id', 'shop_id', 'device_id', 'scheduled_at', 'from', 'to', 'delivery_charge',
         'tax', 'type', 'products_count', 'coupon_amount', 'total_amount', 'coupon_id', 'instructions'
    ];

    /**
     * Get all of the cartProduct for the Cart
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cartProduct()
    {
        return $this->hasMany('App\CartProduct');
    }
    /**
     * Get the shop that owns the Cart
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo('App\Shop');
    }

    /**
     * Get the order associated with the Cart
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function order()
    {
        return $this->hasOne('App\Order');
    }

    /**
     * Get the coupon that owns the Cart
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coupon()
    {
        return $this->belongsTo('App\Coupon');
    }

}
