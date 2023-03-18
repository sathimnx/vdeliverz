<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model
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
         'available', 'image', 'delivery_charge', 'delivery_boy_charge', 'points', 'opens_at', 'closes_at', 'weekdays', 'assign', 'main', 'prior', 'radius'
    ];

    protected $dates = ['opens_at', 'closes_at'];

    public function getBannerImageAttribute($value){
        return env('APP_URL').config('constants.banner_image').$value;
    }
    public function getImageAttribute($value){
        return env('APP_URL').config('constants.shop_image').$value;
    }

    public function getIsOpenedAttribute(){
        $day = now()->format('l');
        $time = now()->format('H:i:s');
        return \App\Shop::where('id', $this->id)->where('opened', 1)->where('active', 1)->whereRaw("FIND_IN_SET('".$day."', weekdays)")
                ->where('opens_at', '<=', $time)
                ->where('closes_at', '>=', $time)
        ->first() ? true : false;
    }

    /**
     * Get the user that owns the Shop
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function cart($device_id){
        if(auth('api')->user()){
                $cart = \App\Cart::where('user_id', auth('api')->user()->id)->where('shop_id', $this->id)->where('checkout', 0)->first();
            }else{
                $cart = \App\Cart::where('device_id', $device_id)->where('shop_id', $this->id)->where('checkout', 0)->first();
            }
            return $cart ? true : false;
    }

    /**
     * The cuisines that belong to the Shop
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cuisines()
    {
        return $this->belongsToMany('App\Cuisine');
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

    /**
     * Get the wishlist associated with the Shop
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function wishlist()
    {
        if(auth('api')->user()){
            $wishlist = \App\Wishlist::where('shop_id', $this->id)->where('user_id', auth('api')->user()->id)->first();
        }
        return isset($wishlist) ? true : false;
    }

   /**
     * Get all of the products for the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
     public function products()
    {
        return $this->hasMany('App\Product');
    }

    public function getAddressAttribute(){
        $address = $this->street.', '.$this->area.', '.$this->city.', '.$this->country;
        return $address;
    }
    public function getRatingAvgAttribute($value){
        $rate = floor($value);
        return $rate;
    }
    public function getOpenTimeAttribute(){
        $rate = date('h:i a', strtotime($this->opens_at));
        return $rate;
    }
    public function getCloseTimeAttribute(){
        $rate = date('h:i a', strtotime($this->closes_at));
        return $rate;
    }
    public function getProductsCountAttribute(){
        $count = $this->products()->count();
        return $count;
    }



    public function getTotalEarningsAttribute(){
        $total = $this->orders()->where('paid', 1)->where('order_status', 3)->sum('amount');
        $total = $total + $this->getTotalDiscountAttribute();
        return $total;
    }
    public function getTotalDeliveredAttribute(){
        $total = $this->orders()->where('paid', 1)->where('order_status', 3)->count();
        return $total;
    }

    public function getCommissionEarningsAttribute(){
        $total = $this->orders()->where('paid', 1)->where('order_status', 3)->sum('comission');
        return $total;
    }
    public function getPenCommissionEarningsAttribute(){
        $total = $this->orders()->where('pay_status', 0)->where('order_status', 3)->sum('comission');
        return $total;
    }
    public function getPaidCommissionEarningsAttribute(){
        $total = $this->orders()->where('pay_status', 1)->where('order_status', 3)->sum('comission');
        return $total;
    }
    public function getPaidEarningsAttribute(){
        $total = $this->orders()->where('pay_status', 1)->where('order_status', 3)->sum('amount');
        $total = $total - $this->getPaidCommissionEarningsAttribute() - $this->getPaidDeliveryChargesAttribute() + $this->getPaidDiscountAttribute();
        return $total;
    }
    public function getPendingEarningsAttribute(){
        $total = $this->orders()->where('pay_status', 0)->where('order_status', 3)->sum('amount');
        $total = $total - $this->getPenCommissionEarningsAttribute() - $this->getPendingChargesAttribute() + $this->getPendingDiscountAttribute();
        return $total;
    }
    public function getPendingDiscountAttribute(){
        $orders = $this->orders()->with('cart')->where('pay_status', 0)->where('order_status', 3)->get();
        $total = 0;
        foreach ($orders as $key => $value) {
            $ta = str_replace(",", "", $value->cart->coupon_amount);
            $total += $ta;
        }
        return $total;
    }
    public function getPaidDiscountAttribute(){
        $orders = $this->orders()->with('cart')->where('pay_status', 1)->where('order_status', 3)->get();
        $total = 0;
        foreach ($orders as $key => $value) {
            $ta = str_replace(",", "", $value->cart->coupon_amount);
            $total += $ta;
        }
        return $total;
    }

    public function getTotalDiscountAttribute(){
        $orders = $this->orders()->with('cart')->where('order_status', 3)->get();
        $total = 0;
        foreach ($orders as $key => $value) {
            $ta = str_replace(",", "", $value->cart->coupon_amount);
            $total += $ta;
        }
        return $total;
    }
    public function getShopEarningsAttribute(){
        $orders = $this->orders()->with('cart')->where('paid', 1)->where('order_status', 3)->get();
        $total = 0;
        foreach ($orders as $key => $value) {
            $ta = str_replace(",", "", $value->cart->total_amount);
            $ca = str_replace(",", "", $value->comission);
            $total += $ta - $ca;
        }
        return $total;
    }
    public function getDeliveryChargesAttribute(){
        $orders = $this->orders()->with('cart')->where('paid', 1)->where('order_status', 3)->get();
        $total = 0;
        foreach ($orders as $key => $value) {
            $dc = str_replace(",", "", $value->cart->delivery_charge);
            $total +=  $dc;
        }
        return $total;
    }
    public function getPaidDeliveryChargesAttribute(){
        $orders = $this->orders()->with('cart')->where('pay_status', 1)->where('order_status', 3)->get();
        $total = 0;
        foreach ($orders as $key => $value) {
            $dc = str_replace(",", "", $value->cart->delivery_charge);
            $total +=  $dc;
        }
        return $total;
    }
    public function getPendingChargesAttribute(){
        $orders = $this->orders()->with('cart')->where('pay_status', 0)->where('order_status', 3)->get();
        $total = 0;
        foreach ($orders as $key => $value) {
            $dc = str_replace(",", "", $value->cart->delivery_charge);
            $total +=  $dc;
        }
        return $total;
    }
    /**
     * Get all of the orders for the Shop
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany('App\Order');
    }

    /**
     * Get all of the slots for the Shop
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function slots()
    {
        return $this->hasMany('App\Slot');
    }

    /**
     * The categories that belong to the Shop
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany('App\Category', 'products', 'shop_id', 'category_id');
    }

    public function getDeliveredCountAttribute(){
        $count = $this->orders()->where('confirmed_at', '!=', null)->where('order_status', 3)->count();
        return $count;
    }
    public function getCanceledCountAttribute(){
        $count = $this->orders()->where('confirmed_at', '!=', null)->where('order_status', 0)->count();
        return $count;
    }
    public function getEarningsAttribute(){
        $earnings = $this->orders()->where('order_status', 3)->sum('amount');
        return $earnings;
    }

    public function titles()
    {
        return $this->hasMany(Title::class);
    }

    /**
     * Get all of the requests for the Shop
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requests()
    {
        return $this->hasMany(Request::class);
    }

    /**
     * The productCategories that belong to the Shop
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function productCategories()
    {
        return $this->belongsToMany(SubCategory::class);
    }

    /**
     * The addresses that belong to the Shop
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function addresses()
    {
        return $this->belongsToMany(Address::class)->withPivot('kms');
    }
}
