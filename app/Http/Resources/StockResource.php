<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
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
            'stock_id' => $this->id,
            'actual_price' => $this->actual_price,
            'selling_price' => $this->price,
            'currency' => $this->currency,
            'variant' => $this->variant,
            'size' => $this->size,
            'unit' => $this->unit,
            'available_count' => $this->available
        ];
    }
}
