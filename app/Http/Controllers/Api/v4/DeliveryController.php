<?php

namespace App\Http\Controllers\Api\v4;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, DB, Auth;
use Carbon\Carbon;

class DeliveryController extends Controller
{
    public function deliveryOrders(){
        try {
            $orders = \App\Order::where('order_status', 1)->with('cart')->orderBy('expected_time', 'desc')->get();
            $order_items = [];

            $notifications = auth('api')->user()->unreadNotifications;
            $data = [];
            $c = 0;
            foreach ($notifications as $key => $value) {
                if(isset($value->data['delivery_order_id'])){
                   $c += 1;
                }
            }
            
            foreach ($orders as $key => $order) {
                $datas = cartProducts($order->cart);
                $ids = explode(', ', $order->rejected);
                if(!in_array(auth()->user()->id, $ids)){
                    $address = json_decode($order->address);
                    $expected = getOrderExpectedTime($order->expected_time, $address->id, $order->shop);
                    $item = [
                        'order_id' => $order->id,
                        'shop_name' => $order->shop->name,
                        'rejected' => $order->rejected,
                        'order_referel' => $order->prefix.$order->id,
                        'total_items' => $order->cart->products_count,
                        'order_amount' => $order->amount,
                        'payment_type' => $order->type,
                        'pick_up' => $order->shop->address,
                        'drop_off' => $address->address,
                        'expected_time' => $order->expected_time->addMinutes(20)->format('H:i'),
                        'expected_date' => $order->expected_time->format('d-m-Y'),
                        
                    ];
                    array_push($order_items, $item);
                }
            }
            return response()->json(['status' => true, 'message' => 'Delivery Orders', 'orders' => $order_items, 'count' => $c]);
        } catch (\Throwable $th) {
            // return apiCatchResponse();
            dd($th);
        }
    }

    public function rejectOrder(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            $order = \App\Order::find($request->order_id);
            $arr = $order->rejected;
            $res = explode(', ', $arr);
            array_push($res, auth()->user()->id);
            $order->rejected = implode(', ', $res);
            $order->save();
            return response()->json(['status' => true, 'message' => 'Order rejected']);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            dd($th);
        }
    }

  public function acceptOrder(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            DB::beginTransaction();
            // $order = \App\Order::where('delivered_by', auth('api')->user()->id)->whereIn('order_status', [1, 2, 4])->first();
            // if($order){
            //     return response()->json(['status' => false, 'message' => 'Kindly deliver picked order']);
            // }
            $order = \App\Order::where('id', $request->order_id)->where('order_status', 1)->first();
            if(!$order){
                return response()->json(['status' => false, 'message' => 'Order already Picked']);
            }
            $order->delivered_by = Auth::user()->id;
            $order->order_status = 4;
            $order->save();
            $order->deliveries()->save(new \App\Delivery(['accepted_at' => now(), 'delivery_type' => Auth::user()->delivery_type, 'user_id' => Auth::user()->id]));
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Order accepted']);
        } catch (\Throwable $th) {
            DB::rollback();
          //  return apiCatchResponse();
            dd($th);
        }
    }

    public function onGoingOrder(){
        try {
            //$ordersId = \App\Order::whereIn('order_status', [4, 2])->pluck('id')->toArray();
            $orders = \App\Order::whereIn('order_status', [4, 2])->where('delivered_by', auth('api')->user()->id)->get();
           // dd($orders);
            if(!$orders){
                return response()->json(['status' => false, 'message' => 'No Orders Found']);
            }
            $OrderDtl = [];
        
               foreach ($orders as $key => $value) {       
                $datas = cartProducts($value->cart);
                   
                $address = json_decode($value->address);
               // dd(getOrderExpectedTime($value->expected_time, $address->id, $value->shop));
                $expected = getOrderExpectedTime($value->expected_time, $address->id, $value->shop);
               
                //$delivery = $value->deliveries();
                $Delvalue = \App\Delivery::where('order_id',$value->id)->latest()->first();
                 //dump($delivery);
                //foreach ($delivery as $Delkey => $Delvalue) {
                    
                    $item = [
                        'order_id' => $value->id,
                        'order_referel' => $value->prefix.$value->id,
                        'total_items' => $value->cart->products_count,
                        'order_amount' => $value->amount,
                        'shop_name' => $value->shop->name,
                        'shop_mobile' => $value->shop->user->mobile,
                        'payment_type' => $value->type,
                        'pick_up' => $value->shop->address,
                        'drop_off' => $address->address,
                        'delivery_status' => $Delvalue->status,
                        'expected_time' => $value->expected_time->addMinutes(20)->format('H:i'),
                        'expected_date' => $value->expected_time->format('d-m-Y'),
                        'cancel_time_min' => $Delvalue->time_left >= 5 || $Delvalue->status != 1  ? 0 : $Delvalue->cancel_time['min'],
                        'cancel_time_sec' => $Delvalue->time_left >= 5 || $Delvalue->status != 1  ? 0 : $Delvalue->cancel_time['sec'],
                        'can_cancel' => $Delvalue->status != 1 ? false : true,
                        'customer_mobile' => $value->user->mobile,
                        'shop_lat' => $value->shop->latitude,
                        'shop_lon' => $value->shop->longitude,
                        'cus_lat' => $address->latitude,
                        'cus_lon' => $address->longitude,
                        'cus_name' => $value->user->name,
                        'products' => $datas['products']
                    ];
                    array_push($OrderDtl, $item);
           //}
                
        }
      // dd($OrderDtl);
            return response()->json(['status' => true, 'message' => 'Ongoing Order', 'order' => $OrderDtl]);
        } catch (\Throwable $th) {
            //return apiCatchResponse();
            dd($th);
        }
    }

    public function orderPicked(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            DB::beginTransaction();
            $delivery = \App\Order::find($request->order_id)->deliveries()->where('user_id', auth('api')->user()->id)->where('status', 1)->first();
            if($delivery){
                $delivery->picked_at = now();
                $delivery->status = 2;
                $delivery->save();
                $delivery->order()->update(['order_status' => 2, 'picked_at' => $delivery->picked_at]);
                DB::commit();
                $VappData = [
                    'fcm' => $delivery->order->shop->user->fcm,
                    'title' => config('constants.order_picked.title'),
                    'body' => 'Vdeliverz Delivery Boy - '.$delivery->user->name.' picked '.$delivery->order->search.' at '.now()->format('d-m-Y H:i'),
                    'icon' => '',
                    'type' => 1
                ];
                $CappData = [
                    'fcm' => $delivery->order->user->fcm,
                    'title' => config('constants.order_picked.title'),
                    'body' => 'Vdeliverz - Delivery, Your order is picked by '.$delivery->user->name.' from the vendor, you will receive it shortly',
                    'icon' => '',
                    'type' => 1
                ];
               // sendSingleAppNotification($VappData, env('VEN_FCM'));
                sendSingleAppNotification($CappData, env('CUS_FCM'));
                return response()->json(['status' => true, 'message' => 'Order Picked']);
            }
            return response()->json(['status' => false, 'message' => 'Order not found']);
        } catch (\Throwable $th) {
            DB::rollback();
            return apiCatchResponse();
            dd($th);
        }
    }

    public function cancelOrder(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            DB::beginTransaction();
            $delivery = \App\Order::find($request->order_id)->deliveries()->where('user_id', auth('api')->user()->id)->where('status', 1)->first();
            if($delivery && $delivery->time_left < 5){
                $charge = \App\Charge::first();
                $delivery->canceled_at = now();
                $delivery->status = 0;
                $delivery->points_earned -= $charge->delivery_points;
                $delivery->amount_earned -= $charge->delivery_charge;
                $delivery->delivery_type = $delivery->user->delivery_type;
                $delivery->save();
                $delivery->order()->update(['order_status' => 1, 'picked_at' => null, 'delivered_by' => null]);
                $poi = $delivery->user->points_earned - $charge->delivery_points;
                $amount = $delivery->user->amount_earned - $charge->delivery_charge;
                $delivery->user()->update(['points_earned' => $poi, 'amount_earned' => $amount]);
                DB::commit();
                $VappData = [
                    'fcm' => $delivery->order->shop->user->fcm,
                    'title' => config('constants.order_cancel.title'),
                    'body' => 'Deliveryboy rejected to pick the order. Shortly will assign a different person to pickup. Sorry for the inconvenience caused.',
                    'icon' => '',
                    'type' => 1,
                ];
                // $CappData = [
                //     'fcm' => $delivery->user->fcm,
                //     'title' => config('constants.order_cancel.title'),
                //     'body' => 'Vdeliverz Order Cancellation, Hey '.$delivery->order->user->name.',  You cancelled this Order '.$delivery->order->search.', Sorry to see you going.',
                //     'icon' => '',
                //     'type' => 1,
                // ];

                // sendSingleAppNotification($CappData, env('CUS_FCM'));
                sendSingleAppNotification($VappData, env('VEN_FCM'));
                return response()->json(['status' => true, 'message' => 'Delivery Canceled']);
            }
            return response()->json(['status' => false, 'message' => 'Delivery Cancel time exceeded']);
        } catch (\Throwable $th) {
            DB::rollback();
            return apiCatchResponse();
            dd($th);
        }
    }

    public function cashNote(Request $request){
        $validator = Validator::make($request->all(), [
                'order_id' => 'required',
                'payment_status' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response()->json(['status' => false, 'message' => $errors]);
        }
        $delivery = \App\Order::find($request->order_id)->deliveries()->where('user_id', auth('api')->user()->id)->where('status', 2)->first();
        if($delivery){
                $charge = \App\Charge::first();
                $delivery->delivered_at = now();
                $delivery->status = 3;
                $delivery->cash_note = $request->cash_note;
                $delivery->points_earned += $charge->delivery_points;
                $delivery->amount_earned += $charge->delivery_charge;
                $delivery->delivery_type = $delivery->user->delivery_type;
                $delivery->payment_status = $request->payment_status;
                $delivery->save();
                $delivery->order()->update(['order_status' => 3, 'paid' => 1, 'delivered_at' => $delivery->delivered_at]);
                $poi = $charge->delivery_points + $delivery->user->points_earned;
                $amount = $charge->delivery_charge + $delivery->user->amount_earned;
                $delivery->user()->update(['points_earned' => $poi, 'amount_earned' => $amount]);
                DB::commit();
                $VappData = [
                    'fcm' => $delivery->order->shop->user->fcm,
                    'title' => config('constants.order_delivered.title'),
                    'body' => 'Vdeliverz - Delivery, '.$delivery->order->search.', is delivered to the '.$delivery->order->user->name.' by '.$delivery->user->name.' at '.now()->format('d-m-Y H:i'),
                    'icon' => '',
                    'type' => 1,
                ];
                $CappData = [
                    'fcm' => $delivery->order->user->fcm,
                    'title' => config('constants.order_delivered.title'),
                    'body' => 'Vdeliverz Delivery, Hurray ! You received your order, Thanks for shopping with Vdeliverz. Shop more and avail more coupons.',
                    'icon' => '',
                    'type' => 1,
                ];

                sendSingleAppNotification($CappData, env('CUS_FCM'));
               // sendSingleAppNotification($VappData, env('VEN_FCM'));
                return response()->json(['status' => true, 'message' => 'Order Delivered Successfully']);
            }
            return response()->json(['status' => false, 'message' => 'Order not found']);
    }

    public function orderDelivered(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            DB::beginTransaction();
            $delivery = \App\Order::find($request->order_id)->deliveries()->where('user_id', auth('api')->user()->id)->where('status', 2)->first();
            if($delivery->order->type == 0){
                return response()->json(['status' => false, 'message' => 'Order in COD']);
            }
            if($delivery){
                $charge = \App\Charge::first();
                $delivery->delivered_at = now();
                $delivery->status = 3;
                $delivery->points_earned += $charge->delivery_points;
                $delivery->amount_earned += $charge->delivery_charge;
                $delivery->delivery_type = $delivery->user->delivery_type;
                $delivery->save();
                $delivery->order()->update(['order_status' => 3, 'paid' => 1, 'delivered_at' => $delivery->delivered_at]);
                $poi = $charge->delivery_points + $delivery->user->points_earned;
                $amount = $charge->delivery_charge + $delivery->user->amount_earned;
                $delivery->user()->update(['points_earned' => $poi, 'amount_earned' => $amount]);
                DB::commit();
                $VappData = [
                    'fcm' => $delivery->order->shop->user->fcm,
                    'title' => config('constants.order_delivered.title'),
                    'body' => 'Vdeliverz - Delivery, '.$delivery->order->search.', is delivered to the '.$delivery->order->user->name.' by '.$delivery->user->name.' at '.now()->format('d-m-Y H:i'),
                    'icon' => '',
                    'type' => 1,
                ];
                $CappData = [
                    'fcm' => $delivery->order->user->fcm,
                    'title' => config('constants.order_delivered.title'),
                    'body' => 'Vdeliverz Delivery, Hurray ! You received your order, Thanks for shopping with Vdeliverz. Shop more and avail more coupons.',
                    'icon' => '',
                    'type' => 1,
                ];

                sendSingleAppNotification($CappData, env('CUS_FCM'));
                sendSingleAppNotification($VappData, env('VEN_FCM'));
                return response()->json(['status' => true, 'message' => 'Order Delivered Successfully']);
            }
            return response()->json(['status' => false, 'message' => 'Order not found']);
        } catch (\Throwable $th) {
            DB::rollback();
            return apiCatchResponse();
            dd($th);
        }
    }

    public function deliveredOrders(){
        try {
            $deliveries = Auth::user()->deliveries()->where('status', 3)->get();
           
            $delivered = [];
            $average = $deliveries->sum('points_earned');
            foreach ($deliveries as $key => $value) {
                
                $delivery = [
                    'order_id' => $value->order_id,
                    'order_referel' => $value->order->prefix.$value->order_id,
                    'time' => date('h:i a', strtotime($value->delivered_at)),
                    'date' => date('d-M-Y', strtotime($value->delivered_at)),
                    'date_time' => $value->delivered_at,
                ];
                //dd($delivery);
            
                array_push($delivered, $delivery);
            
            }
           
            return response()->json(['status' => true, 'message' => 'Delivered Orders', 'delivery_boy_type' => Auth::user()->delivery_type == 1 ? false : true, 'points' => $average, 'orders' => $delivered]);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            dd($th);
        }
    }

    public function deliveredSearch(Request $request){
        try {
            $validator = Validator::make($request->all(), [
            'search' => 'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }

//            $order_id = substr($request->search,6);
            $orders = \App\Order::where('delivered_by', auth('api')->user()->id)->where('search', 'LIKE', '%'. $request->search .'%')->where('order_status', 3)->get();
            $delivered = [];
            $deliveries = Auth::user()->deliveries()->where('status', 3)->get();
            $average = $deliveries->sum('points_earned');
            foreach ($orders as $key => $value) {
                $delivery = [
                    'order_id' => $value->id,
                    'order_referel' => $value->prefix.$value->id,
                    'time' => date('h:i a', strtotime($value->delivered_at)),
                    'date' => date('d-M-Y', strtotime($value->delivered_at)),
                    'date_time' => $value->delivered_at,
                ];
                array_push($delivered, $delivery);
            }
            return response()->json(['status' => true, 'message' => 'Search Delivered Orders', 'delivery_boy_type' => Auth::user()->delivery_type == 1 ? false : true, 'points' => $average, 'orders' => $delivered]);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            dd($th);
        }
    }

    public function canceledOrders(){
        try {
            $deliveries = Auth::user()->deliveries()->where('status', 0)->get();
            $delivered = [];
            foreach ($deliveries as $key => $value) {
                $delivery = [
                    'order_id' => $value->order_id,
                    'order_referel' => $value->order->prefix.$value->order->id,
                    'time' => date('h:i a', strtotime($value->canceled_at)),
                    'date' => date('d-M-Y', strtotime($value->canceled_at)),
                    'date_time' => $value->canceled_at,
                ];
                array_push($delivered, $delivery);
            }
            return response()->json(['status' => true, 'message' => 'Canceled Orders', 'orders' => $delivered]);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            dd($th);
        }
    }

    public function orderDetail(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            $delivery = Auth::user()->deliveries()->where('status', 3)->where('order_id', $request->order_id)->first();
            if(!$delivery){
                return response()->json(['status' => false, 'message' => 'Order not found']);
            }
            $data = cartProducts($delivery->order->cart);
            $original = json_decode($delivery->order->address);
            $order_details = [
                'order_id' => $delivery->order_id,
                'order_referel' => $delivery->order->prefix.$delivery->order_id,
                'earned' => $delivery->points_earned,
                'products' => $data['products'],
                'item_total' => sizeof($data['products']),
                'sub_total' => $delivery->order->cart->total_amount,
                'tax' => $delivery->order->cart->tax,
                'coupon' => $delivery->order->cart->coupon_amount,
                'delivery_charge' => $delivery->order->cart->delivery_charge,
                'total_amount' => $delivery->order->amount,
                'shop_name' => $delivery->order->shop->name,
                'pick_up' => $delivery->order->shop->address,
                'drop_off' => $original->address,
                'payment_type' => $delivery->order->type,
                'delivery_boy_type' => $delivery->delivery_type == 1 ? false : true,
            ];

            return response()->json(['status' => true, 'message' => 'Order Detail', 'order_details' => $order_details]);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            dd($th);
        }
    }

    public function notifications(){
        try {
            $notifications = auth('api')->user()->unreadNotifications;
            $data = [];
            foreach ($notifications as $key => $value) {
                if(isset($value->data['delivery_order_id'])){
                $order = \App\Order::where('id', $value->data['delivery_order_id'])->first();
                if($order){
                    $notify = [
                        "notify_id" => $value->notifiable_id,
                        "notify_head" => 'New Order Arrived',
                        'description' => 'Order referel : '.$order->prefix.$order->id,
                        'time' => Carbon::createFromTimeStamp(strtotime($value->created_at))->diffForHumans(),
                        ];
                        array_push($data, $notify);
                }
                }
            }
            auth('api')->user()->unreadNotifications->markAsRead();
            return response()->json(['status' => true, 'message' => 'Arrived Orders', 'data' => $data]);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            dd($th);
        }
    }

    public function searchDelivery(Request $request){
        $validator = Validator::make($request->all(), [
            'search' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response()->json(['status' => false, 'message' => $errors]);
        }

        $orders = \App\Order::where('shop_address->address', 'LIKE', '%'. $request->search . '%')->orWhere('search', 'LIKE', '%'. $request->search .'%');
        $orders = $orders->where('order_status', 1)->get();
        $order_items = [];

            foreach ($orders as $key => $order) {
                $ids = explode(', ', $order->rejected);
                if(!in_array(auth()->user()->id, $ids)){
                    $address = json_decode($order->address);
                    $expected = getOrderExpectedTime($order->expected_time, $address->id, $order->shop);
                    $item = [
                        'order_id' => $order->id,
                        'rejected' => $order->rejected,
                        'order_referel' => $order->prefix.$order->id,
                        'total_items' => $order->cart->products_count,
                        'order_amount' => $order->amount,
                        'payment_type' => $order->type,
                        'pick_up' => $order->shop->address,
                        'drop_off' => $address->address,
                        'expected_time' => $order->expected_time->addMinutes(20)->format('H:i'),
                        'expected_date' => $order->expected_time->format('d-m-Y'),
                    ];
                    array_push($order_items, $item);
                }
            }
            return response()->json(['status' => true, 'message' => 'Search Orders', 'orders' => $order_items]);
    }
}
