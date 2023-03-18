<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubService extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'active', 'image', 'service_id'
    ];

    public function getImageAttribute($value){
        return env('APP_URL').config('constants.demand_sub_service_icon_image').$value;
    }
    public function getDelImageAttribute(){
        return env('APP_URL').config('constants.demand_sub_service_icon_image');
    }

    /**
     * Get the service that owns the SubService
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
        return $this->belongsTo('App\Service');
    }

    public function getMinHourPriceAttribute(){
        return \App\ProviderService::where('sub_service_id', $this->id)->min('hour');
    }
    public function getMinJobPriceAttribute(){
        return \App\ProviderService::where('sub_service_id', $this->id)->min('job');
    }

    /**
     * The providers that belong to the SubService
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function providers()
    {
        return $this->belongsToMany(Provider::class)->withPivot(['id', 'hour', 'job']);
    }
}
