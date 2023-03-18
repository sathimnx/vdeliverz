<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cuisine extends Model
{
/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = [
        'name', 'active'
    ];
    /**
     * The shops that belong to the Cuisine
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function shops()
    {
        return $this->belongsToMany('App\Shop');
    }

    /**
     * Get all of the products for the Cuisine
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany('App\Product');
    }
}
