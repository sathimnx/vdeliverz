<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable, HasRoles, HasApiTokens, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'mobile', 'image', "provider_id", 'amount_earned',
         'points_earned', 'delivery_type', 'provider_name', 'otp', 'active', 'verified', 'fcm', 'otp', 'rating_count', 'rating_avg'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getImageAttribute($value){
        return env('APP_URL').config('constants.user_profile_img').$value;
    }

    public function getUsernameAttribute(){
        if($this->email){
            $username = $this->email;
        }elseif($this->name){
            $username = $this->name;
        }elseif($this->mobile){
            $username = $this->mobile;
        }
        return $username;
    }

    /**
     * Get all of the addresses for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function addresses()
    {
        return $this->hasMany('App\Address');
    }
    /**
     * Get all of the deliveries for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deliveries()
    {
        return $this->hasMany('App\Delivery');
    }
    public function getDeliveredCountAttribute(){
        $count = $this->deliveries()->where('status', 3)->count();
        return $count;
    }
    public function getCanceledCountAttribute(){
        $count = $this->deliveries()->where('status', 0)->count();
        return $count;
    }
    /**
     * Get the shop associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function shop()
    {
        return $this->hasOne('App\Shop');
    }

    /**
     * Get the provider associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function provider()
    {
        return $this->hasOne('App\Provider');
    }

    public function getNotifyCountAttribute(){
        return $this->unreadNotifications->count();
    }
    public function getDemandNotifyCountAttribute(){
        return $this->unreadNotifications()->where('data->demand', true)->count();
    }

    public function getWishlistCountAttribute(){
        return $this->shops->count();
    }
    public function getProvidersCountAttribute(){
        return $this->providers->count();
    }

    /**
     * The shops that belong to the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function shops()
    {
        return $this->belongsToMany('App\Shop', 'wishlists', 'user_id', 'shop_id');
    }

    /**
     * The providers that belong to the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function providers()
    {
        return $this->belongsToMany('App\Provider', 'wishlists', 'user_id', 'provider_id');
    }

    /**
     * Get the onGoing associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function onGoing()
    {
        return $this->hasOne(Order::class, 'delivered_by', 'id')->whereIn('order_status', [4, 2]);
    }

    /**
     * The providers that belong to the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cars()
    {
        return $this->belongsToMany('App\CarProvider', 'wishlists', 'user_id', 'car_provider_id');
    }

    public function cart(){
        $cart = \App\Cart::where('user_id', $this->id)->where('checkout', 0)->first();
        return $cart;
    }


  /**
     * Get all of the vendorOrders for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function vendorOrders()
    {
        return $this->hasManyThrough(Order::class, Shop::class);
    }
    /**
     * Get all of the products for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function products()
    {
        return $this->hasManyThrough('App\Product', 'App\Shop');
    }

    /**
     * Get all of the orders for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany('App\Order');
    }
    /**
     * Get the currentOrder associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function currentOrder()
    {
        $order = \App\Order::where('user_id', auth('api')->user()->id)->where('confirmed_at', '!=', null)->where('delivered_at', null)->first();
        return $order;
    }

    public function getIsAvailableAttribute()
    {
        $order = \App\Order::where('delivered_by', $this->id)->whereIn('order_status', [4, 2])->first();
        return $order ? false : true;
    }

    /**
     * Get all of the bookings for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

     public function mainShop()
    {
        $shop = \App\Shop::where('user_id', $this->id)->where('main', 1)->first();
        return $shop;
    }
    /**
     * Get all of the shops for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function outlets()
    {
        return $this->hasMany(Shop::class);
    }

    public function AauthAcessToken(){
        return $this->hasMany('\App\OauthAccessToken');
    }

    /**
     * Get all of the reviews for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'delivery_boy_id', 'id');
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