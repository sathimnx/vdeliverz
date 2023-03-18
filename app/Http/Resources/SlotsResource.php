<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SlotsResource extends JsonResource
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
            'slot_id' => $this->id,
            'from' => $this->from,
            'to' => $this->to,
            'weekdays' => explode(',', $this->weekdays),
            'active' => $this->active,
            'shop_name' => $this->shop->name
        ];
    }
}