<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShopResource extends JsonResource
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
                'shop_id' => $this->id,
                'shop_name' => $this->name,
                'shop_image' => $this->image,
                'shop_area' => $this->area,
                'shop_address' => $this->street.', '.$this->area.', '.$this->city,
                'rating' => $this->rating_avg,
                'rating_count' => $this->rating_count,
                'is_wishlist' => $this->wishlist(),
        ];
    }
}
