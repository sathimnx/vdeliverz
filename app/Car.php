<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'image', 'active', 'service_id'
    ];

    public function getImgUrlAttribute(){
        return env('APP_URL').config('constants.demand_car_image').$this->image;
    }
    public function getMinDayPriceAttribute(){
        return \App\CarProvider::where('car_id', $this->id)->min('day');
    }
    public function getMinMonthPriceAttribute(){
        return \App\CarProvider::where('car_id', $this->id)->min('month');
    }

    /**
     * Get the service that owns the Car
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    /**
     * The providers that belong to the Car
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function providers()
    {
        return $this->belongsToMany(Provider::class)->withPivot(['id', 'day', 'week', 'month', 'image']);
    }
}
