<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OutletShowResource extends JsonResource
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
            'is_opened' => $this->is_opened,
            'primary' => $this->main,
            'vendor_name' => $this->user->name,
            'mobile' => $this->user->mobile,
            'status' => $this->active
        ];
    }
}