<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Title extends Model
{
    protected $fillable = [
        'name', 'shop_id', 'active'
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function toppings()
    {
        return $this->hasMany(Topping::class);
    }
}
