<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    public $data;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = cartProducts($this->cart);
        return [
            "order_id" => $this->id,
            "paid" => $this->paid,
            "payment_type" => $this->type ? 'Online' : 'COD',
            "amount" => $this->amount,
            "without_deliverycharge" => $this->cart->total_amount,
            "currency" => $this->currency,
            "mobile" => $this->user->mobile ?? NULL,
            "customer_address" => json_decode($this->address),
            "canceled_at" => $this->canceled_at ?? '',
            "cancel_reason" => $this->cancel_reason,
            "confirmed_at" => $this->confirmed_at ? $this->confirmed_at->format('d-m-Y H:i') : '',
            "delivered_at" => $this->delivered_at ? $this->delivered_at->format('d-m-Y H:i') : '' ,
            "referral" => $this->search,
            "picked_at" => $this->picked_at ? $this->picked_at->format('d-m-Y H:i') : '',
            "order_status" => $this->order_status,
            'order_state' => $this->order_state,
            "delivered_by" => $this->delivered_at ? $this->delivered_at->format('d-m-Y H:i') : '',
            "expected_time" => $this->expected_time->addMinutes(30)->format('d-m-Y H:i'),
            "shop_address" => json_decode($this->shop_address),
            "comission" => $this->comission,
            "accepted_at" => $this->accepted_at ? $this->accepted_at->format('d-m-Y H:i') : '',
            "rejected_at" => $this->rejected_at ? $this->rejected_at->format('d-m-Y H:i') : '',
            "assigned_at" => $this->assigned_at ? $this->assigned_at->format('d-m-Y H:i') : '',
            "instructions" => $this->cart->instructions ?? '',
            "cancel_reason" => $this->cancel_reason,
            'delivery_boy' => $this->order_status == 3 ? $this->delivered_by ? $this->deliveredBy->name : 'Manual Delivery' : 'Not Yet Delivered',
            "order_items" => productsList($data['products'])
        ];
    }
}
