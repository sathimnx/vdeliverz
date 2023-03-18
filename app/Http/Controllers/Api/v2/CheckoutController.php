<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
use Auth, DB, Validator;
use App\Notifications\OrderNotification;
use Carbon\Carbon;

class CheckoutController extends Controller
{
    public function payment_initiate(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            $local_order = \App\Order::where('id', $request->order_id)->where('confirmed_at', null)->first();
            if(!$local_order){
                return response(['status' => false, 'message' => 'Invalid Order!']);
            }
            $total = $local_order->amount;
            $order_reffer_id = $local_order->prefix.$local_order->id;
            $api_key = env('RAZ_PAY_KEY');
            $api_secret = env('RAZ_PAY_SECRET');
            $api = new Api($api_key, $api_secret);

            $order  = $api->order->create([
                'receipt'         => $order_reffer_id,
                'amount'          => $total * 100, // amount in the smallest currency unit
                'currency'        => 'INR', // <a href="/docs/payment-gateway/payments/international-payments/#supported-currencies" target="_blank">See the list of supported currencies</a>.)
            ]);

            return response()->json(['status' => true, 'message' => 'Razorpay order id Created', 'cart_id' => $local_order->cart_id, 'order_id' => $order['id'], 'amount' => $order['amount'],
                'api_key' => env('RAZ_PAY_KEY'),
            ]);
        } catch (\Throwable $th) {
            dd($th);
        }
    }
}