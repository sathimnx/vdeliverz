<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
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
            "car_id" => $this->id,
            "name" => $this->name,
            'image' => $this->img_url,
            'min_day_price' => $this->min_day_price
        ];
    }
}
