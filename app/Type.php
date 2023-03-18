<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Type extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = [
        'name', 'active', 'image'
    ];

    /**
     * Get all of the categories for the Type
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function categories()
    {
        return $this->hasManyThrough('App\Category', 'App\Shop');
    }
}
