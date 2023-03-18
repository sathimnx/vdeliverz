<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shop_id', 'order_ids', 'tot_amt', 'tot_com', 'tot_charge', 'amt_paid'
    ];
}
