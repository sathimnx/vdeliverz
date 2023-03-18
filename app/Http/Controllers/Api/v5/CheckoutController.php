<?php

namespace App\Http\Controllers\Api\v5;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
use Auth, DB, Validator;
use App\Notifications\OrderNotification;
use Carbon\Carbon;
use Anand\LaravelPaytmWallet\Facades\PaytmWallet;
use App\Order;

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
          
          $rand = rand(1000, 9999);
          $ldate = date('YmdHis');
          //dd($ldate);
          
            /*$order_reffer_id = $local_order->prefix.$local_order->id;
            $api_key = env('RAZ_PAY_KEY');
            $api_secret = env('RAZ_PAY_SECRET');
            $api = new Api($api_key, $api_secret);
            
              $order  = $api->order->create([
                'receipt'         => $order_reffer_id,
                'amount'          => $total * 100, // amount in the smallest currency unit
                'currency'        => 'INR', // <a href="/docs/payment-gateway/payments/international-payments/#supported-currencies" target="_blank">See the list of supported currencies</a>.)
            ]);*/

          //  dump($total);
            $total_amount = str_replace(',','',number_format($total,2));
           // dd($total_amount);
            $paytmParams = array();

            $paytmParams["body"] = array(
                "requestType"   => "Payment",
                "mid"           => env('PAYTM_MERCHANT_ID'),
                "websiteName"   => env('PAYTM_MERCHANT_WEBSITE'),
                "orderId"       => $request->order_id.$ldate,
                "callbackUrl"   => env('PAYTM_CALLBACK').$request->order_id.$ldate,
                //"callbackUrl"   => "https://vdeliverz.gdigitaldelivery.com/callback.php",
                "txnAmount"     => array(
                    "value"     => $total_amount,
                    "currency"  => "INR",
                ),
                "userInfo"      => array(
                    "custId"    => "CUST_001".$rand,
                ),
                // "enablePaymentMode" => array(
                //                     array(
                //                         "mode" => "UPI,BALANCE",
                //                         "channels" => ["UPI","UPIPUSH","UPIPUSHEXPRESS", ],
                                        
                //                         ),
                //                 ),
               
            );
            
            //dump($paytmParams);
            
            /*
            * Generate checksum by parameters we have in body
            * Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys 
            */
            $checksum = generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), env('PAYTM_MERCHANT_KEY'));
           // dump($checksum);
            $paytmParams["head"] = array(
                "signature"    => $checksum
            );
            
            $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);
            

            $url = env('PAYTM_API_URL').env('PAYTM_MERCHANT_ID')."&orderId=".$request->order_id.$ldate;
          
            //dump($url);
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json")); 
            $response = json_decode(curl_exec($ch));
            //dd($response->body);
            
            
            if($response->body->resultInfo->resultStatus == 'S'){
                 return response()->json(['status' => true, 'message' => $response->body->resultInfo->resultMsg, 'cart_id' => $local_order->cart_id,'token' => $response->body->txnToken,'mid' => env('PAYTM_MERCHANT_ID'),'pay_amount' => $total, 'order_id' => $request->order_id.$ldate]);
            }else{
                 return response()->json(['status' => false, 'message' => $response->body->resultInfo->resultMsg, 'cart_id' => $local_order->cart_id,'mid' => env('PAYTM_MERCHANT_ID')]);
            }
            
            
            

          
           /* return response()->json(['status' => true, 'message' => 'Paytm order token Created', 'cart_id' => $local_order->cart_id, 'order_id' => $order['id'], 'amount' => $order['amount'],'token' => $response->body->txnToken,
                'api_key' => env('RAZ_PAY_KEY'),
            ]);*/
            
             return response()->json(['status' => true, 'message' => 'Paytm order token Created', 'cart_id' => $local_order->cart_id,'token' => $response->body->txnToken,'mid' => env('PAYTM_MERCHANT_ID')]);
        } catch (\Throwable $th) {
            dd($th);
        }
    }
    
    
    public function payment_callback(Request $request){
        
        DB::beginTransaction();
        //return $request->all();
        $order = Order::find($request->order_id);
        $order->payment_status = json_encode($request->all());
        $order->save();
       DB::commit();
         return response()->json(['status'=> true,'order' =>$order->payment_status], 200);
    }
}
