<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    // use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = [
        'name', 'active', 'image', 'type_id', 'order', 'banner'
    ];

    public function getImageAttribute($value){
        return env('APP_URL').config('constants.category_icon_image').$value;
    }

    public function getBannerAttribute($value){
        return env('APP_URL').config('constants.category_banner_image').$value;
    }
    /**
     * The shops that belong to the Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function shops()
    {
        return $this->belongsToMany('App\Shop', 'products', 'category_id', 'shop_id');
    }

    /**
     * Get all of the shops for the Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    // public function shops()
    // {
    //     return $this->hasManyThrough(Shop::class, Product::class);
    // }

    /**
     * Get the type that owns the Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo('App\Type');
    }

}