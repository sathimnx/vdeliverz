<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = [
        'product_id', 'quantity', 'price', 'currency', 'variant', 'size', 'actual_price'
    ];

    /**
     * Get the product that owns the Stock
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo('App\Product');
    }
}