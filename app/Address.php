<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
     use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = [
        'user_id', 'address', 'latitude', 'longitude', 'landmark', 'type','google_address'
    ];

    /**
     * Get the user that owns the Address
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * The shops that belong to the Address
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function shops()
    {
        return $this->belongsToMany(Shop::class)->withPivot('kms');
    }
}