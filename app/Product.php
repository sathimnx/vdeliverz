<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    // use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = [
        'name', 'active', 'shop_id', 'category_id', 'variety', 'sub_category_id', 'price', 'currency', 'description', 'available', 'image',  'rec'
    ];

    public function getImageAttribute($value){
        return env('APP_URL').config('constants.product_image').$value;
    }
   // public function getVarietyAttribute($value){
  //      return $value === 'veg' ? 1 : 0;
  //  }

    public function getCanCustomizeAttribute(){
        $toppings = $this->toppings()->where('available', '>', 0)->get();
        $stocks = $this->stocks()->where('available', '>', 0)->get();
        if($toppings->isNotEmpty() || count($stocks) > 1){
            return 1;
        }
        return 0;
    }

    /**
     * Get the cuisine that owns the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cuisine()
    {
        return $this->belongsTo('App\Cuisine');
    }



    /**
     * Get all of the stocks for the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stocks()
    {
        return $this->hasMany('App\Stock');
    }
    /**
     * Get the shop that owns the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo('App\Shop');
    }

    /**
     * Get the category that owns the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    /**
     * Get the subCategory that owns the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subCategory()
    {
        return $this->belongsTo('App\SubCategory');
    }

    public function toppings()
    {
        return $this->hasMany(Topping::class);
    }

}