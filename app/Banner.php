<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'active', 'image', 'slogan'
   ];

   public function getImageAttribute($value){
       return env('APP_URL').config('constants.app_banner_image').$value;
   }
}
