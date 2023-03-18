<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Provider extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = [
        'name', 'active', 'user_id', 'type_id', 'latitude', 'opened', 'longitude', 'banner_image',
         'price', 'rating_count', 'rating_avg', 'street', 'area', 'city', 'country', 'currency', 'description', 'comission',
         'available', 'image', 'delivery_charge', 'delivery_boy_charge', 'points', 'opens_at', 'closes_at', 'weekdays', 'assign', 'hour_price',
         'job_price', 'email', 'c_car', 'c_ser'
    ];
    public function getBannerImageAttribute($value){
        return env('APP_URL').config('constants.demand_banner_image').$value;
    }
    public function getImageAttribute($value){
        return env('APP_URL').config('constants.demand_shop_image').$value;
    }

    public function getIsAvailableAttribute(){
        $day = now()->format('l');
        $time = now()->format('H:i:s');
        $provider = \App\Provider::where('id', $this->id)->where('opened', 1)->where('active', 1)->whereRaw("FIND_IN_SET('".$day."', weekdays)")
        ->where('opens_at', '<=', $time)
        ->where('closes_at', '>=', $time)->first();
        return $provider ? true : false;
    }
    public function getHourAttribute(){
        $hour = \App\ProviderService::where('provider_id', $this->id)->min('hour');
        return $hour;
    }
    public function getJobAttribute(){
        $job = \App\ProviderService::where('provider_id', $this->id)->min('job');
        return $job;
    }



    /**
     * Get the user that owns the Provider
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The subServices that belong to the Provider
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function subServices()
    {
        return $this->belongsToMany(SubService::class)->withPivot(['id', 'hour', 'job']);
    }
    /**
     * The cars that belong to the Provider
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cars()
    {
        return $this->belongsToMany(Car::class);
    }
    /**
     * Get all of the slots for the Provider
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function slots()
    {
        return $this->hasMany(Slot::class);
    }
    /**
     * Get the wishlist associated with the Shop
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function wishlist()
    {
        if(auth('api')->user()){
            $wishlist = \App\Wishlist::where('provider_id', $this->id)->where('user_id', auth('api')->user()->id)->first();
        }
        return isset($wishlist) ? true : false;
    }

    /**
     * Get all of the reviews for the Provider
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function recalculateRating()
    {
        $reviews = $this->reviews();
        $avgRating = $reviews->avg('rating');
        $this->rating_avg = round($avgRating,1);
        $this->rating_count = $reviews->count();
        $this->save();
    }
}