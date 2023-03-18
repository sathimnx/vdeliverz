<?php

namespace App\Http\Controllers\Api\v6;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB, Validator, Auth, Hash;
use App\User;

class DoorToDoorDeliveryController extends Controller
{

    public function doorTodoor_deliveryBooking(Request $request){
        try
        {
            $validator = Validator::make($request->all(), [
                'pickup_name'=>'required',
                'pickup_date'=>'required',
                'pickup_time'=>'required',
                'pickup_mobile'=>'required',
                'pickup_address'=>'required',
                'pickup_address_area'=>'required',
                'pickup_address_landmark'=>'required',
                'pickup_address_addresstype'=>'required',
                'pickup_address_latitude'=>'required',
                'pickup_address_longitude'=>'required',
                'pickup_item_name'=>'required',
                'pickup_item_dtl'=>'required',
                'pickup_item_quantity'=>'required',
                'pickup_item_image'=>'required',
                'transportation_type'=>'required',
              
                'droupup_name'=>'required',
                'droupup_mobile'=>'required',
                'droupup_address'=>'required',
                'droupup_address_area'=>'required',
                'droupup_address_landmark'=>'required',
                'droupup_address_addresstype'=>'required',
                'droupup_address_latitude'=>'required',
                'droupup_address_longitude'=>'required',
            ]);
            if($validator->fails()){
                flash()->error($validator->messages()->first());
                return back()->withInput();
            }
            
            $pickup_address=array('name'=>$request->pickup_name,'address'=>$request->pickup_address,'area'=>$request->pickup_address_area,'landmark'=>$request->pickup_address_landmark,'address_type'=>$request->pickup_address_addresstype,
            'latitude'=>$request->pickup_address_latitude,'longitude'=>$request->pickup_address_longitude,'phone_number'=>$request->pickup_mobile);
            
            $Pickup_addressId = DB::table('dTd_pickup_address_dtls')->insertGetId($pickup_address);
            
             if($request->hasFile('pickup_item_image')){
              if($request->file('pickup_item_image')->isValid())
                {

                    $extension = $request->pickup_item_image->extension();
                    $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->pickup_item_name)).time()."cat." .$extension;
                    $request->pickup_item_image->move(config('constants.category_icon_image'), $file_path);
                }
            }
            
            $pickup_itemDetail=array('item_name'=>$request->pickup_item_name,'item_detail'=>$request->pickup_item_dtl,'quantity'=>$request->pickup_item_quantity,'image'=>$file_path);
            
            $Pickup_itemId = DB::table('dTd_pickup_dtls')->insertGetId($pickup_itemDetail);
            
            $dropup_address=array('name'=>$request->droupup_name,'address'=>$request->droupup_address,'area'=>$request->droupup_address_area,'landmark'=>$request->droupup_address_landmark,'address_type'=>$request->droupup_address_addresstype,
            'latitude'=>$request->droupup_address_latitude,'longitude'=>$request->droupup_address_longitude,'phone_number'=>$request->droupup_mobile);
            
            $Dropup_addressId = DB::table('dTd_drop_address_dtls')->insertGetId($dropup_address);
            
            $DoorToDoorDelivery=array('pickup_date'=>$request->pickup_date,'pickup_time'=>$request->pickup_time,'pickup_mobile'=>$request->pickup_mobile,'pickup_addressId'=>$Pickup_addressId,
            'pickup_itemId'=>$Pickup_itemId,'transportation_type'=>$request->transportation_type,
            'droupup_mobile'=>$request->droupup_mobile,'droupup_addressId'=>$Dropup_addressId);
            
            $order_id = DB::table('door_to_doorDelivery')->insertGetId($DoorToDoorDelivery);
            
            $dTd_delivery_orders = DB::table('door_to_doorDelivery')->where('order_id',$order_id)->first();
            
            $items = [];
               if(isset($dTd_delivery_orders) ) { 
                 $item = [
                        'pickup_date' => $dTd_delivery_orders->pickup_date,
                        'pickup_time' => $dTd_delivery_orders->pickup_time,
                        'pickup_mobile' => $dTd_delivery_orders->pickup_mobile,
                        'transportation_type' => $dTd_delivery_orders->transportation_type,
                        'droupup_mobile'=> $dTd_delivery_orders->droupup_mobile,
                 ];
               }
                array_push($items, $item);
           
            $Pickup_addressDtl = DB::table('dTd_pickup_address_dtls')->where('id',$dTd_delivery_orders->pickup_addressId)->first();
            $Dropup_addressDtl = DB::table('dTd_drop_address_dtls')->where('id',$dTd_delivery_orders->droupup_addressId)->first();
            $Pickup_itemDtl = DB::table('dTd_pickup_dtls')->where('id',$dTd_delivery_orders->pickup_itemId)->first();
            
            $latitude1 = $Pickup_addressDtl->latitude;$longitude1 = $Pickup_addressDtl->longitude;
            $latitude2 = $Dropup_addressDtl->latitude;$longitude2 = $Dropup_addressDtl->longitude;$unit = 'miles';
            
              $theta = $longitude1 - $longitude2; 
              $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta))); 
              $distance = acos($distance); 
              $distance = rad2deg($distance); 
              $distance = $distance * 60 * 1.1515; 
              switch($unit) { 
                case 'miles': 
                  break; 
                case 'kilometers' : 
                  $distance = $distance * 1.609344; 
              } 
              $Distance =  (round($distance,2)); 
            
            $charge = \App\Charge::first();
            
            $Delivery_charge = $Distance * $charge->dTd_extracharge; $tax=0; $grand_total = $Delivery_charge + $tax;
            
            DB::table('door_to_doorDelivery')->where('order_id',$order_id)->update(array('distance' => $Distance,'tax' => 0,'grand_total' => $grand_total)); 
            
            return response()->json(['status' => true, 'message' => 'Door To Door delivery booking completed', 'dtd_deliverydtl' =>$items, 'pickup_address' => $Pickup_addressDtl,'droup_address' => $Dropup_addressDtl,'items_dtl'=>$Pickup_itemDtl,'delivery_charge'=>round($Delivery_charge),
            'tax' => $tax, 'grand_total' => round($grand_total)]);
            
           // return response()->json(['status' => true,  'message' => 'Door to Door delivery booking completed.']);
            
        }
        catch (\Throwable $th) {
           // return apiCatchResponse();
            dd($th);
        }
    }
    
    public function doorTodoor_orderPlaced(Request $request){
        try
        {
             $validator = Validator::make($request->all(), [
                'order_id'=>'required',
            ]);
            if($validator->fails()){
                flash()->error($validator->messages()->first());
                return back()->withInput();
            }
            
            
            $dTd_delivery_orders = DB::table('door_to_doorDelivery')->where('order_id',$request->order_id)->first();
            
            $Pickup_addressDtl = DB::table('dTd_pickup_address_dtls')->where('id',$dTd_delivery_orders->pickup_addressId)->first();
            $Dropup_addressDtl = DB::table('dTd_drop_address_dtls')->where('id',$dTd_delivery_orders->droupup_addressId)->first();
            $Pickup_itemDtl = DB::table('dTd_pickup_dtls')->where('id',$dTd_delivery_orders->pickup_itemId)->first();
            
            $charge = \App\Charge::first();
            //1-Order Placed
            DB::table('door_to_doorDelivery')->where('order_id',$request->order_id)->update(array('order_status' => 1)); 
            
             return response()->json(['status' => true, 'message' => 'Order Placed',  'pickup_address' => $Pickup_addressDtl,'droup_address' => $Dropup_addressDtl,'items_dtl'=>$Pickup_itemDtl,
             'delivery_charge'=>round($dTd_delivery_orders->distance * $charge->dTd_extracharge),'tax' => $dTd_delivery_orders->tax, 'grand_total' => round($dTd_delivery_orders->grand_total)]);
        }
        catch (\Throwable $th) {
           // return apiCatchResponse();
            dd($th);
        }
    }
    
      public function payment_initiate(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
           
            $local_order = DB::table('door_to_doorDelivery')->where('order_id',$request->order_id)->first();
            if(!$local_order){
                return response(['status' => false, 'message' => 'Invalid Order!']);
            }
            $total = $local_order->grand_total;
          
            $rand = rand(1000, 9999);
            $ldate = date('YmdHis');
            $total_amount = str_replace(',','',number_format($total,2));
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
             
            );
           
            $checksum = generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), env('PAYTM_MERCHANT_KEY'));
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
                 return response()->json(['status' => true, 'message' => $response->body->resultInfo->resultMsg, 'order_id' => $local_order->order_id,'token' => $response->body->txnToken,
                 'mid' => env('PAYTM_MERCHANT_ID'),'pay_amount' => $total, 'order_id' => $request->order_id.$ldate]);
            }else{
                 return response()->json(['status' => false, 'message' => $response->body->resultInfo->resultMsg, 'order_id' => $local_order->order_id,'mid' => env('PAYTM_MERCHANT_ID')]);
            }
            
            
            
             return response()->json(['status' => true, 'message' => 'Paytm order token Created', 'order_id' => $local_order->order_id,'token' => $response->body->txnToken,'mid' => env('PAYTM_MERCHANT_ID')]);
        } catch (\Throwable $th) {
            dd($th);
        }
    }
    
    
    public function payment_callback(Request $request){
        
        DB::beginTransaction();
        //return $request->all();
        $order = DB::table('door_to_doorDelivery')->where('order_id',$request->order_id)->update(array('payment_status' => json_encode($request->all())));
        
        DB::commit();
         return response()->json(['status'=> true,'order' =>$order->payment_status], 200);
    }
}