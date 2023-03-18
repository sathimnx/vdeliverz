<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CuisineResource extends JsonResource
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
            'cuisine_id' => $this->id,
            'cuisine_name' => $this->name
        ];
    }
}
