<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OutletResource extends JsonResource
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
            'is_opened' => $this->opened ? true : false,
            'primary' => $this->main,
            // 'pagination' => [
            //     'total' => $this->total(),
            //     'count' => $this->count(),
            //     'per_page' => $this->perPage(),
            //     'current_page' => $this->currentPage(),
            //     'total_pages' => $this->lastPage()
            // ],
        ];
    }
}
