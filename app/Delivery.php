<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = [
        'user_id', 'order_id', 'canceled_at', 'delivered_at', 'cash_note', 'picked_at', 'cancel_reason', 'status', 'accepted_at'
    ];

    public function getDeliveryStatusAttribute(){
        $text = 'Processing';
        if($this->delivered_at){
            $text = $this->delivered_at.' ( Delivered )';
        }
        if($this->canceled_at){
            $text = $this->canceled_at.' ( Canceled )';
        }
        return $text;
    }

    /**
     * Get the order that owns the Delivery
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo('App\Order');
    }

    public function getTimeLeftAttribute()
    {
        $from = \Carbon\Carbon::now();
        $to = \Carbon\Carbon::parse($this->accepted_at);
        $time = $from->diffInMinutes($to);
        return $time;
    }
    public function getCancelTimeAttribute()
    {
        $from = \Carbon\Carbon::parse($this->accepted_at)->addMinutes(5);
        $to = \Carbon\Carbon::now();
        $time = $from->diff($to);
        return ['min' => $time->i, 'sec' => $time->s];
    }

    /**
     * Get the user that owns the Delivery
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}