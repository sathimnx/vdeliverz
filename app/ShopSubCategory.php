<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShopSubCategory extends Model
{
    protected $table = 'shop_sub_category';

    protected $fillable = [
        'shop_id', 'sub_category_id', 'order', 'active'
    ];

    /**
     * Get the shop that owns the ShopSubCategory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
    /**
     * Get the subCategory that owns the ShopSubCategory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function getProdCountAttribute(){
        return \App\Product::where('shop_id', $this->shop_id)->where('sub_category_id', $this->sub_category_id)->count();
    }
}