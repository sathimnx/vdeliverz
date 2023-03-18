<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
// use Illuminate\Database\Eloquent\SoftDeletes;

class CarProvider extends Model
{
    // use SoftDeletes;

    protected $table = 'car_provider';

    protected $fillable = [
        'provider_id', 'car_id', 'day', 'week', 'month', 'about', 'image', 'active', 'rating_count', 'rating_avg', 'deposit'
    ];
    public $timestamps = false;

    public function getImgUrlAttribute(){
        return env('APP_URL').config('constants.demand_ca_pro_image').$this->image;
    }

    /**
     * Get the wishlist associated with the Shop
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function wishlist()
    {
        if(auth('api')->user()){
            $wishlist = \App\Wishlist::where('user_id', auth('api')->user()->id)->where('car_provider_id', $this->id)->first();
        }
        return isset($wishlist) ? true : false;
    }

    /**
     * Get the provider that owns the ProviderService
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * Get the car te ProviderService
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function car()
    {
        return $this->belongsTo(Car::class);
    }
    /**
     * Get all of the specs for the CarProvider
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function specs()
    {
        return $this->hasMany(About::class, 'car_provider_id', 'id');
    }

    public function hasBooking($from, $to, $device_id){
        $bookings = \App\Booking::where('car_provider_id', $this->id)->where('car_id', $this->car_id)->whereIn('status', [1, 2])->get();
        foreach ($bookings as $key => $booking) {
            if(($booking->pick_up >= $from && $booking->pick_up <= $to) || ($booking->drop_off >= $from && $booking->drop_off <= $to) )
            {
                return true;
            }
        }
        return false;
    }

    /**
     * Get all of the bookings for the CarProvider
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    /**
     * Get all of the reviews for the Shop
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviews()
    {
        return $this->hasMany('App\Review');
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