<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shop_id', 'order_ids', 'accepted_by', 'completed_by', 'accept_ins', 'complete_ins', 'request_ins', 'total', 'com', 'charge',
        'requested_at', 'accepted_at', 'completed_at', 'count', 'pay_status', 'bank_id'
    ];

    protected $dates = [
        'requested_at', 'accepted_at', 'completed_at'
    ];

    public function getPayStateAttribute(){
        switch ($this->pay_status) {
            case 2:
                $text = 'Accepted';
                break;
            case 3:
                $text = 'Completed';
                break;
            default:
                $text = 'Requested';
                break;
        }
        return $text;
    }
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    /**
     * Get the shop that owns the Request
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}