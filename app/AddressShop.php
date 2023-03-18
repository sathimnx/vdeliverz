<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AddressShop extends Model
{
    protected $table = 'address_shop';

    protected $fillable = [
        'address_id', 'shop_id', 'kms', 'alg'
    ];
    public $timestamps = false;

    /**
     * Get the address that owns the AddressShop
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * Get the shop that owns the AddressShop
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
