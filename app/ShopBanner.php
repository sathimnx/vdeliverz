<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShopBanner extends Model
{

    protected $fillable = [
        'active', 'image', 'shop_id', 'order'
   ];

   public function getImageAttribute($value){
       return env('APP_URL').config('constants.app_shop_banner_image').$value;
   }


   public function shop()
   {
       return $this->belongsTo(Shop::class);
   }
}
