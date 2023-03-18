<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CuisineResource;

class ShopDetailResource extends JsonResource
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
            "shop_id" => $this->id,
            "vendor_mobile" => $this->user->mobile,
            "name" => $this->name,
            "verified" => $this->verified,
            "username" => $this->user->name,
            "latitude" => $this->latitude,
            "longitude" => $this->longitude,
            "price" => $this->price,
            "rating_count" => $this->rating_count,
            "rating_avg" => $this->rating_avg,
            "currency" => $this->currency,
            "street" => $this->street,
            "area" => $this->area,
            "city" => $this->city,
            "country" => $this->country,
            "image" => $this->image,
            "description" => $this->description,
            "email" => $this->email,
            "mobile" => $this->mobile,
            "banner_image" => $this->banner_image,
            "opened" => $this->opened,
            "delivery_boy_charge" => $this->delivery_boy_charge,
            "delivery_charge" => $this->delivery_charge,
            "points" => $this->points,
            "opens_at" => $this->opens_at->format('H:i'),
            "closes_at" => $this->closes_at->format('H:i'),
            "weekdays" => explode(',', $this->weekdays),
            "comission" => $this->comission,
            "radius" => $this->radius,
            "min_amount" => $this->min_amount,
            "assign" => $this->assign,
            "main" => $this->main,
            'cuisines' => CuisineResource::collection($this->cuisines)
        ];
    }
}