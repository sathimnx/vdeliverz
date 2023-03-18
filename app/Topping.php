<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Topping extends Model
{
    protected $fillable = [
        'product_id', 'name', 'price', 'active', 'title_id', 'variety', 'title_name'
    ];

    public function getVarietyAttribute($value){
        return $value === 'veg' ? 1 : 0;
    }

    public function title()
    {
        return $this->belongsTo(Title::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
