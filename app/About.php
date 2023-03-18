<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class About extends Model
{
    protected $table = 'about';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'car_provider_id', 'name', 'icon', 'icon_url'
    ];

    public function getIconAttribute($value){
        return env('APP_URL').config('constants.demand_ca_pro_about_image').$value;
    }

    /**
     * Get the carProvider that owns the About
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function carProvider()
    {
        return $this->belongsTo(carProvider::class);
    }
}
