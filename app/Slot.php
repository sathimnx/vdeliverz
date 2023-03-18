<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Slot extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'from', 'to', 'shop_id', 'active', 'weekdays', 'count', 'available'
    ];

    public function getDaysAttribute(){
        $days = implode(', ', unserialize($this->weekdays));
        return $days;
    }
    public function getFromTimeAttribute(){
        return date('h:i A', strtotime($this->from));
    }
    public function getToTimeAttribute(){
        return date('h:i A', strtotime($this->to));
    }

    public function resetAvailability(){
        $reset = $this->count - $this->available;
        return $reset;
    }
    /**
     * Get the shop that owns the Slot
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo('App\Shop');
    }
    /**
     * Get the provider that owns the Slot
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}
