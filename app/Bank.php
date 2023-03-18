<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable = [
        'active', 'icon', 'shop_id', 'name', 'acc_no', 'bank_name', 'branch', 'city', 'ifsc'
   ];

   /**
    * Get the shop that owns the Bank
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
   public function shop()
   {
       return $this->belongsTo(Shop::class);
   }
}