<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
        'product_id' => $this->id,
        "name" => $this->name,
        "shop_id" => $this->shop_id,
        "shop_name" => $this->shop->name,
        "category_id" => $this->category_id,
        'category_name' => $this->category->name,
        "sub_category_id" => $this->sub_category_id,
        'sub_category_name' => $this->subCategory->name,
        "active" => $this->active,
        "description" => $this->description,
        "image" => $this->image,
        "variety" => $this->variety,
        "cuisine_id" => $this->cuisine_id,
        ];
    }
}
