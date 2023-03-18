<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \Carbon\Carbon;

class Booking extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'provider_id', 'type', 'prefix', 'address_details', 'rejected_at', 'device_id', 'status', 'booked_at', 'slot_id', 'from', 'to',
         'payment', 'total_amount', 'payable_amount', 'amount_paid', 'taxes', 'travel_charge', 'coupon_amount', 'instructions',
          'cancelled_at', 'confirmed_at', 'cancel_reason', 'accepted_at', 'assigned_at', 'completed_at', 'charge', 'provider_details',
         'paid', 'amount', 'payment_id', 'address_id', 'currency', 'comission', 'search', 'hours', 'commission', 'provider_sub_service_id',
         'car_provider_id', 'id_proof', 'address_proof', 'pick_type', 'drop_off', 'pick_up', 'car_details', 'car_name', 'car_id', 'days'
    ];

    public function getReferralAttribute()
    {
        return $this->prefix.$this->id;
    }
    public function getApiBookAttribute()
    {
        return Carbon::parse($this->booked_at)->format('d/m/Y');
    }

    /**
     * Get the address that owns the Booking
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function getCarPriceAttribute(){
        switch ($this->type) {
            case 1:
                $text = 'Day';
                break;
            case 2:
                $text = 'Week';
                break;
            case 3:
                $text = 'Month';
                break;

            default:
                $text = '';
                break;
        }
        return $text;
    }
    public function getBookTypeAttribute()
    {
        switch ($this->type) {
            case 0:
                $text = 'Hourly';
                break;

            default:
                $text = 'Job';
                break;
        }
        return $text;
    }

    public function getPickDateAttribute(){
        return Carbon::parse($this->pick_up)->format('Y-m-d');
    }
    public function getPickTimeAttribute(){
        return Carbon::parse($this->pick_up)->format('h:i A');
    }
    public function getDropDateAttribute(){
        return Carbon::parse($this->drop_off)->format('Y-m-d');
    }
    public function getDropTimeAttribute(){
        return Carbon::parse($this->drop_off)->format('h:i A');
    }
    /**
     * Get the provider that owns the Booking
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
    /**
     * Get the user that owns the Booking
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getBookStateAttribute()
    {
       if($this->status === 0){
           $text = 'Not Placed';
       }
       if($this->status === 1){
           $text = 'Pending';
       }
       if($this->status === 2){
           $text = 'Ongoing';
       }
       if($this->status === 3){
           $text = 'Ongoing';
       }
       if($this->status === 4){
           $text = 'Cancelled by Vendor';
       }
       if($this->status === 5){
           $text = 'Cancelled';
       }
        if($this->status === 6){
            $text = 'Completed';
        }
       return $text ?? null;
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
        $from = \Carbon\Carbon::parse($this->confirmed_at)->addMinutes(5);
        $to = \Carbon\Carbon::now();
        $time = $from->diff($to);
        return ['min' => $time->i, 'sec' => $time->s];
    }

}
