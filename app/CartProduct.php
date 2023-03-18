<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CartProduct extends Model
{
    protected $table = 'cart_products';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = [
        'product_id', 'stock_id', 'product_details', 'stock_details', 'cart_id', 'count', 'amount', 'product_name', 'toppings', 'toppings_total'
    ];
    /**
     * Get the cart that owns the CartProduct
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cart()
    {
        return $this->belongsTo('App\Cart');
    }
    /**
     * Get the stock that owns the CartProduct
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stock()
    {
        return $this->belongsTo('App\Stock');
    }
}
