<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = [
        'user_id', 'shop_id', 'rating', 'comment', 'provider_id', 'car_provider_id', 'delivery_boy_id', 'order_id'
    ];

    public function getTimeagoAttribute()
    {
        $date = \Carbon\Carbon::createFromTimeStamp(strtotime($this->created_at))->diffForHumans();
        return $date;
    }
    public function storeReviewForShop($shop_id, $rating)
    {
        $review = $this->where('shop_id', $shop_id)->where('user_id', auth('api')->user()->id)->first();
        if($review){
            $review->update(['rating' => $rating]);
        }else{
            $this->create(['user_id' => auth('api')->user()->id, 'shop_id' => $shop_id, 'rating' => $rating]);
        }
        $shop = \App\Shop::find($shop_id);
        $shop->recalculateRating();
    }

    public function storeReviewForDelivery($user_id, $order_id, $rating, $comments)
    {
        $review = $this->where('order_id', $order_id)->where('delivery_boy_id', $user_id)->where('user_id', auth('api')->user()->id)->first();
        if($review){
            $review->update(['rating' => $rating, 'comment' => $comments]);
        }else{
            $this->create(['user_id' => auth('api')->user()->id, 'delivery_boy_id' => $user_id, 'rating' => $rating, 'order_id' => $order_id, 'comment' => $comments]);
        }
        $user = \App\User::find($user_id);
        $user->recalculateRating();
    }

    public function storeReviewForProvider($provider_id, $rating, $comments)
    {
        $review = $this->where('provider_id', $provider_id)->where('user_id', auth('api')->user()->id)->first();
        if($review){
            $review->update(['rating' => $rating, 'comment' => $comments]);
        }else{
            $this->create(['user_id' => auth('api')->user()->id, 'provider_id' => $provider_id, 'rating' => $rating, 'comment' => $comments]);
        }
        $provider = \App\Provider::find($provider_id);
        $provider->recalculateRating();
    }

    public function storeReviewForCar($car_provider_id, $rating, $comments)
    {
        $review = $this->where('car_provider_id', $car_provider_id)->where('user_id', auth('api')->user()->id)->first();
        if($review){
            $review->update(['rating' => $rating, 'comment' => $comments]);
        }else{
            $this->create(['user_id' => auth('api')->user()->id, 'car_provider_id' => $car_provider_id, 'rating' => $rating, 'comment' => $comments]);
        }
        $carProvider = \App\CarProvider::find($car_provider_id);
        $carProvider->recalculateRating();
    }

    /**
     * Get the user that owns the Review
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    /**
     * Get the deliveryBoy that owns the Review
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deliveryBoy()
    {
        return $this->belongsTo(User::class, 'delivery_boy_id', 'id');
    }

    /**
     * Get the shop that owns the Review
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo('App\Shop');
    }
    /**
     * Get the provider that owns the Review
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}