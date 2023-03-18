<?php

namespace App;

use App\Review;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     
     use SoftDeletes;
     protected $fillable = [
        'user_id', 'cart_id', 'type', 'prefix', 'shop_address', 'rejected', 'earned', 'picked_at', 'delivered_at',
         'expected_time', 'delivered_by', 'canceled_at', 'confirmed_at', 'cancel_reason', 'accepted_at', 'assigned_at',
         'paid', 'amount', 'payment_id', 'address', 'currency', 'comission', 'order_status', 'search', 'points_earned', 'rejected',
         'delivery_type', 'amount_earned', 'pay_status', 'd_charge', 'v_amt', 'razorpay_payment_id', 'razorpay_order_id', 'razorpay_signature', 'kms'
    ];

    protected $dates = [
        'delivered_at', 'assigned_at', 'canceled_at', 'rejected_at', 'picked_at', 'accepted_at', 'confirmed_at', 'expected_time'
    ];

    public function getShopEarnedAttribute(){
        $value = str_replace(",", "", $this->cart->total_amount) - $this->comission;
        return $value;
    }

    public function getOrderedAtAttribute()
    {
        $date = \Carbon\Carbon::parse($this->confirmed_at)->format('d M Y').' at '.\Carbon\Carbon::parse($this->confirmed_at)->format('h:i a');
        return isset($this->confirmed_at) ? $date : null;
    }
    public function getTakenAtAttribute()
    {
        $date = \Carbon\Carbon::parse($this->picked_at)->format('d M Y').' at '.\Carbon\Carbon::parse($this->picked_at)->format('h:i a');
        return isset($this->picked_at) ? $date : null;
    }
    public function getCompletedAtAttribute()
    {
        $date = \Carbon\Carbon::parse($this->delivered_at)->format('d M Y').' at '.\Carbon\Carbon::parse($this->delivered_at)->format('h:i a');
        return isset($this->delivered_at) ? $date : null;
    }

    public function getCustomerRatingAttribute(){
        return Review::where('order_id', $this->id)->where('delivery_boy_id', $this->delivered_by)->pluck('rating')->first();
        // return $
    }
    public function getVendorRatingAttribute(){
        return Review::where('order_id', $this->id)->where('user_id', $this->shop->user_id)->pluck('rating')->first();
        // return $
    }
    public function getCustomerCommentAttribute(){
        return Review::where('order_id', $this->id)->where('delivery_boy_id', $this->delivered_by)->pluck('comment')->first();
        // return $
    }
    public function getVendorCommentAttribute(){
        return Review::where('order_id', $this->id)->where('user_id', $this->shop->user_id)->pluck('comment')->first();
        // return $
    }



    public function getOrderStateAttribute()
    {
       if($this->order_status == 0){
           $text = 'Canceled';
       }
       if($this->order_status == 1){
           $text = 'Accepted and Assigned';
       }
       if($this->order_status == 2){
           $text = 'Out for delivery';
       }
       if($this->order_status == 3){
           $text = 'Delivered';
       }
       if($this->order_status == 4){
           $text = 'Yet to Pick';
       }
       if($this->order_status == 5){
           $text = 'Accepted';
       }
        if($this->order_status == 6){
            $text = 'Rejected by Vendor';
        }
        if($this->order_status == 7){
            $text = 'Not Assigned';
        }
        if($this->order_status == 8){
            $text = 'Order Created';
        }
        if($this->order_status == 9){
            $text = 'Order Confirmed';
        }
        if($this->order_status == 10){
            $text = 'Delivery Champ has accepted';
        }
        if($this->order_status == 11){
            $text = 'Food preparation in progress';
        }
        if($this->order_status == 12){
            $text = 'Food packing in progress ';
        }
        
       return $text ?? null;
    }

    /**
     * Get the shop that owns the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo('App\Shop');
    }

    /**
     * Get the deliveredBy that owns the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deliveredBy()
    {
        return $this->belongsTo('App\User', 'delivered_by', 'id');
    }

    /**
     * Get all of the deliveries for the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deliveries()
    {
        return $this->hasMany('App\Delivery');
    }

    public function getTimeLeftAttribute()
    {
        $from = \Carbon\Carbon::now();
        $to = \Carbon\Carbon::parse($this->confirmed_at);
        $time = $from->diffInMinutes($to);
        return $time;
    }
    public function getCancelTimeAttribute()
    {
        $from = \Carbon\Carbon::parse($this->confirmed_at)->addMinutes(1);
        $to = \Carbon\Carbon::now();
        $time = $from->diff($to);
        return ['min' => $time->i, 'sec' => $time->s];
    }

 public function getCanCancelAttribute(){
        if ($this->order_status == 7) {
            return true;
        }
        if($this->getTimeLeftAttribute() <= 1){
            return false;
        }
        return false;
    }

    /**
     * Get the user that owns the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Get the cart that owns the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cart()
    {
        return $this->belongsTo('App\Cart');
    }


}
