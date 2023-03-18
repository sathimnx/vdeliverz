<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Charge extends Model
{

    protected $fillable = [
        'delivery_charge', 'customer_charge', 'delivery_points', 'active', 'basic_charge', 'extra_charge', 'basic_km','gst_charge'
    ];

    public function setBasicChargeAttribute($value){
        // dd($value);
        $this->attributes['basic_charge'] = $value ? $value * 100 : null;
    }

    public function setExtraChargeAttribute($value){
        $this->attributes['extra_charge'] = $value ? $value * 100 : null;
    }

    public function getBasicChargeAttribute($value){
        return $value;
    }

    public function getExtraChargeAttribute($value){
        return $value ;
    }
}
