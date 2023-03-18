<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    // use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'active', 'image', 'order'
    ];

    public function getImageAttribute($value){
        return env('APP_URL').config('constants.demand_service_icon_image').$value;
    }

    /**
     * Get all of the subServices for the Service
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subServices()
    {
        return $this->hasMany('App\SubService');
    }
    /**
     * Get all of the cars for the Service
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cars()
    {
        return $this->hasMany(Car::class);
    }

}
