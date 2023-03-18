<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'withdrawal_id' => $this->id,
            'withdrawal_referral' => '0545813'.$this->id,
            'payment_state' => $this->pay_state,
            'status' => $this->pay_status,
            'amount' => $this->total.' ₹',
            'date' => $this->created_at->format('Y-m-d'),
            'bank_name' => $this->bank->bank_name,
            'acc_no' => $this->bank->acc_no,
            'acc_name' => $this->bank->name
        ];
    }
}