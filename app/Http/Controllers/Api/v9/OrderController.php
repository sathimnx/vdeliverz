<?php

namespace App\Http\Controllers\Api\v9;

use App\Order;
use Notification;
use Carbon\Carbon;
use App\AddressShop;
use Razorpay\Api\Api;
use Validator, DB, Auth;
use Illuminate\Http\Request;
use App\Events\PostPublished;
use App\Notifications\AdminOrder;
use \Spatie\Permission\Models\Role;
use App\Notifications\OrderConfirm;
use App\Http\Controllers\Controller;
use \Spatie\Permission\Models\Permission;
use App\Notifications\DeliveryNotification;
use Razorpay\Api\Errors\SignatureVerificationError;

class OrderController extends Controller
{
    public function index(){
        if(Auth::user()->hasAnyRole('admin')){
            \App\Order::where('id', $request->order_id)->first();
            $data['Order'] = Order::paginate(10);
        }elseif(Auth::user()->hasAnyRole('vendor')){
            $data['Order'] = Order::where('shop_id', Auth::user()->id)->paginate(10);
        }else{
            abort(404);
        }
        // if (request()->ajax()) {
        //     return view('/orders', array('shops' => $data['shops']))->render();
        // }
        return view('/orders', $data);
    }

    public function orderSummary(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'address_id' => 'required',
                //'coupon_code' => 'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            
            //coupon start(10-05-2022)
            
        $coupon_code = $request->coupon_code;
        $userId = auth('api')->user()->id;
        if($coupon_code != null)
        {
           if(auth('api')->user()){
                $cart = \App\Cart::where('user_id',$userId)->where('checkout', 0)->first();
            }
            
         switch (true) {       
        	case ((\App\Coupon::whereRaw("BINARY `coupon_code`= ?",[$coupon_code])->first() == null)):
        		    return response(['status' => true, 'message' => 'Coupon code is not valid!','available' => true]);
        		break;
        
        	case (\App\Coupon::whereDate('expired_on', '>=', now())->first() == null):
        		 return response(['status' => true, 'message' => 'Coupon is expired!','available' => true]);
        		break;
        
        	case (\App\Coupon::where('max_order_amount', '<=', $cart->total_amount)->first() == null):
        		 return response(['status' => true, 'message' => 'You order amount is not eligible to apply this order!','available' => true]);
        		break;
        
            case (\App\Coupon::whereRaw("BINARY `coupon_code`= ?",[$coupon_code])->whereDate('expired_on', '>=', now())
                            ->where('max_order_amount', '<=', $cart->total_amount)->first() == null ):
        		 return response(['status' => true, 'message' => 'Coupon is not valid!','available' => true]);
        		break;
        		
        	default:
        		echo '';
        		break;
        
         }
            $coupon = \App\Coupon::whereRaw("BINARY `coupon_code`= ?",[$coupon_code])->whereDate('expired_on', '>=', now())
                        ->where('max_order_amount', '<=', $cart->total_amount)->first();
           
           
            if(auth('api')->user()){
                $already = \App\Cart::where('user_id', $userId)->where('checkout', 1)->where('coupon_id', $coupon->id)->first();
            }
            if($already){
                return response(['status' => true, 'message' => 'Coupon Not Available!','available' => true]);
            }
            
            $cart->coupon_id = $coupon->id;
            $discount = round(( str_replace(',', '', $cart->total_amount) / 100 ) * $coupon->coupon_percentage, 2);
            $cart->coupon_amount = $discount;
            $discount_total = round(str_replace(',', '', $cart->total_amount) - $discount, 2);
            $cart->coupon_details = json_encode($coupon);
            $cart->save();
          }
            //coupon end
            $address = \App\Address::where('id', $request->address_id)->where('user_id', $userId)->first();
         
            if(!$address){
                return response()->json(['status' => false, 'message' => 'Address not found for this user', 'available' => true]);
            }
            $cart = \App\Cart::where('user_id', $userId)->where('checkout', 0)->with('order')->first();
            if(!$cart->scheduled_at){
                $cart->scheduled_at = now();
                $cart->from = now();
                $cart->to = now()->addMinutes(20);
            }

            $shop_address = [
                'latitude' => $cart->shop->latitude,
                'longitude' => $cart->shop->longitude,
                'address' => $cart->shop->street.', '.$cart->shop->area.', '.$cart->shop->city,
            ];
            $address_shop = AddressShop::where('address_id', $address->id)->where('shop_id', $cart->shop->id)->where('alg', 0)->first();
            if($address_shop){
                $distance['int_dis'] = $address_shop->kms;
            }else{
                $distance = saveAddressDistance($address, $cart->shop);
            }
            
            $cart->delivery_charge = calculateDeliveryCharge(ceil($distance['int_dis']));
            $cart->save();
            if($distance['int_dis'] > $cart->shop->radius){
               return response()->json(['status' => false, 'message' => 'Delivery not available for this location', 'available' => false]);
            }
            if(isset($cart->order->id)){
                $order = $cart->order;
                $except = isset($cart->scheduled_at) ? $cart->scheduled_at.' '.$cart->from : now();
                $order->expected_time = $except;
            }else{
                $order = new Order;
                $order->shop_id = $cart->shop_id;
                $order->cart_id = $cart->id;
                $except = isset($cart->scheduled_at) ? $cart->scheduled_at.' '.$cart->from : now();
                $order->expected_time = $except;
            }
            $gst_charge = $cart->shop->gst == 1 ? calculateGSTCharge(str_replace(',', '', $cart->total_amount)) : 0;
            
            $order->user_id = $userId;
            $order->kms = $distance['int_dis'];
            $order->type = 0;
            $order->prefix = '058454';
            $tc = str_replace(',', '', $cart->total_amount);
            $dc = $cart->delivery_charge ? str_replace(',', '', $cart->delivery_charge) : 0;
            $ca = $cart->coupon_amount ? str_replace(',', '', $cart->coupon_amount) : 0;
            $gst = $gst_charge ? str_replace(',', '', $gst_charge) : 0;
            
            $order->TotalAmt = $tc;
            $order->amount = $tc + $dc + $gst + $cart->tax - $ca;
            $order->address = json_encode($address);
            $order->shop_address = json_encode($shop_address);
            $order->save();
            $data = cartProducts($cart);
            $original = json_decode($order->address);
            $estimate = canShowShop($original->id, $order->shop);
            $order_details = [
                'order_id' => $order->id,
                'order_number' => $order->prefix.$order->id,
               'address' => $original->address,
                'delivery_time' => $data['delivery_time'],
                'estimated_time' => $order->cart->type == 0 ? $estimate['time'] : Carbon::parse($order->cart->from)->format('h:i a').' - '.Carbon::parse($order->cart->to)->format('h:i a'),
                'is_scheduled' => $order->cart->type,
            ];
            $price_details = [
                'items_total' => $data['total_items'],
                'sub_total' => number_format($data['grand_total'], 2),
                'tax' => $data['tax'],
                'delivery_charge' => number_format($data['delivery_charge'], 2),
                'coupon_discount' => number_format($data['coupon_discount'], 2),
                'total' => number_format($data['total'], 2),
                ];
            $coupons = \App\Coupon::whereDate('expired_on', '>=', now())->get();
            
            return response()->json(['status' => true, 'message' => 'Order Summary', 'items' => $data['products'], 
            'order_details' => $order_details, 'price_details' => $price_details, 'available' => false,'coupons' => $coupons]);
        } catch (\Throwable $th) {
           // DB::rollback();
           // return apiCatchResponse();
            dd($th);
        }
    }

    public function filter($shop, $type,$ordertype){
        if($shop == 'all' && $type == 'all' && $ordertype =='all' && $date =='all'){
            return redirect('/orders');
        }
            $data['Order'] = \App\Order::all();

            $FilterShop = (auth()->user()->hasAnyRole('admin')) ?  ($shop == 'all' ?  $data['Order'] : \App\Request::where('shop_id', $shop)->paginate(10)) :
            (\App\Request::where('shop_id', auth()->user()->shop->id)->paginate(10));
            $FilterType = $type == 'all' ? $FilterShop : $FilterShop.where('order_status',$type)->paginate(10);
            $FilterOrderType = $ordertype == 'all' ?  $FilterType : $FilterType.where('type',$ordertype)->paginate(10);
           // $FilterDeliveryboy = $ordertype == 'all' ?  $FilterType : $FilterType.where('type',$FilType)->paginate(10);
          // $FilterDate = $date == 'all' ?  $FilterOrderType : $FilterOrderType.where('delivered_at',$date)->paginate(10);
         
        if (request()->ajax()) {
            return view('order.order_table', array('FilteredOrders' => $FilterOrderType))->render();
        }

        return view('order.orders', $data);
    }

    public function orderConfirm(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required',
                'payment_type' => 'required|integer',
                'razorpay_signature' => 'required_if:payment_type,1',
                'razorpay_payment_id' => 'required_if:payment_type,1',
                'razorpay_order_id' => 'required_if:payment_type,1',
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            DB::beginTransaction();
            $order = \App\Order::where('id', $request->order_id)->where('user_id', auth('api')->user()->id)->where('canceled_at', null)->first();
            if(!$order || (isset($order->cart->checkout) && $order->cart->checkout == 1)){
                return response()->json(['status' => false, 'message' => 'Invalid Order id']);
            }
            $order->type = $request->payment_type;
            if($request->payment_type == 1){
                $api_key = env('RAZ_PAY_KEY');
                $api_secret = env('RAZ_PAY_SECRET');
                $api = new Api($api_key, $api_secret);
                $attributes  = array(
                    'razorpay_signature'  => $request->razorpay_signature,
                    'razorpay_payment_id'  => $request->razorpay_payment_id,
                    'razorpay_order_id' => $request->razorpay_order_id
                );
                $raz_order  = $api->utility->verifyPaymentSignature($attributes);
            }
            $order->razorpay_signature = $request->razorpay_signature;
            $order->razorpay_order_id = $request->razorpay_order_id;
            $order->razorpay_payment_id = $request->razorpay_payment_id;
            $order->paid = $request->payment_type;
            //$order->order_status = $order->shop->assign == 1 ? 1 : 7;
            $order->order_status = $order->shop->assign == 1 ? 8 :7;
            $tc = str_replace(',', '', $order->cart->total_amount);
            $order->comission = round(($tc / 100) * $order->shop->comission, 2);
            
            $gst_charge = $order->shop->gst == 1 ? calculateGSTCharge(str_replace(',', '', $order->cart->total_amount)) : 0;           
            $order->cart->gst = round($gst_charge);
            $order->search = $order->prefix.$order->id;
            $order->payment_id = $request->payment_id ?? $order->payment_id;
            // $order->amount = isset($request->amount_paid) ? $request->amount_paid : $order->amount;
            $order->confirmed_at = now();
            $order->save();
            $order->cart()->update(['checkout' => 1]);
            $user = Auth::user();
            Notification::send($user, new OrderConfirm($order));
            $data = cartProducts($order->cart);
            $original = json_decode($order->address);
            $estimate = canShowShop($original->id, $order->shop);
            $order_details = [
                'order_id' => $order->id,
                'order_number' => $order->prefix.$order->id,
                'address' => $original->address,
                'payment_type' => $order->type,
                'delivery_time' => $data['delivery_time'],
                'estimated_time' => $order->cart->type == 0 ? $estimate['time'] : Carbon::parse($order->cart->from)->format('h:i a').' - '.Carbon::parse($order->cart->to)->format('h:i a'),
                'is_scheduled' => $order->cart->type,
            ];
            $price_details = [
                'items_total' => number_format($data['total_items'], 2),
                'sub_total' => number_format($data['grand_total'], 2),
                'tax' => $data['tax'],
                'delivery_charge' => number_format($data['delivery_charge'], 2),
                'coupon_discount' => number_format($data['coupon_discount'], 2),
                'total' => number_format($data['total'], 2),
                ];
                if($order->shop->assign == 1){
                    $appData = [
                        'users' => \App\User::role('delivery-boy')->where('fcm', '!=', null)->get(),
                        'title' => 'New Order Arrived.',
                        'body' => 'Order refferal : '.$order->search,
                        'icon' => '',
                        'type' => 1
                    ];
                    sendAppNotification($appData, env('DEL_FCM'));
                    $users = \App\User::role('delivery-boy')->where('active', 1)->get();
                    Notification::send($users, new DeliveryNotification($order));
                }
                $arra = ['order_number' => $order->search];
                $title = getContent($arra, config('constants.order_placed.title'));
                $content = getContent($arra, config('constants.order_placed.v_push'));

                $VappData = [
                    'fcm' => $order->shop->user->fcm,
                    'title' => $title,
                    'body' => $content,
                    'icon' => '',
                    'type' => 1,
                ];
                sendSingleAppNotification($VappData, env('VEN_FCM'));
                $admins = \App\User::role(['admin'])->where('active', 1)->get();
                $shop_admin = \App\User::where('id', $order->shop->user_id)->get();
                $admins = collect($admins)->merge($shop_admin);
                Notification::send($admins, new AdminOrder($order));
                DB::commit();
                event(new PostPublished(['id' => $order->id, 'prefix' => $order->prefix]));
            return response()->json(['status' => true, 'message' => 'Order Placed', 'items' => $data['products'], 'order_details' => $order_details, 'price_details' => $price_details  ]);
        } catch (\Throwable $th) {
            // DB::rollback();
            return apiCatchResponse();
            dd($th);
        }
    }


    public function orderDetails(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            $order = \App\Order::find($request->order_id);
            $data = cartProducts($order->cart);
            $order = [
                   'order_id' => $order->id,
                   'order_status' => $order->order_status,
                   'shop_details' => $data['shop_details'],
                   'product_details' => productsList($data['products']),
                   'total_amount' => $order->TotalAmt== null ? 0 :$order->TotalAmt,
                    'is_scheduled' => $order->cart->type,

                ];
            return response()->json(['status' => true, 'message' => 'Order Detail', 'order_details' => [$order]]);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            //throw $th;
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
            $order = \App\Order::where('id', $request->order_id)->where('user_id', Auth::user()->id)->first();
            if(!$order || $order->time_left >= 5){
                return response()->json(['status' => false, 'message' => 'Order cancel time exceed']);
            }
            $order->canceled_at = now();
            $order->order_status = 0;
            $order->save();
            foreach($order->cart->cartProduct as $item){
                $stock = \App\Stock::find($item->stock_id);
                $stock->available += $item->count;
                $stock->save();
            }
            $appData = [
                'users' => \App\User::role('delivery-boy')->where('id', $order->delivered_by)->get(),
                'title' => 'Order Canceled by customer.',
                'body' => 'Order refferal : '.$order->search,
                'icon' => '',
                'type' => 2
            ];
            $VappData = [
                'fcm' => $order->shop->user->fcm,
                'title' => 'Order Canceled by Customer.',
                'body' => 'Order refferal : '.$order->search,
                'icon' => '',
                'type' => 2
            ];
            $CappData = [
                'fcm' => $order->user->fcm,
                'title' => config('constants.order_cancel.title'),
                'body' => 'Vdeliverz Order Cancellation, Hey '.$order->user->name.',  You cancelled this Order '.$order->search.', Sorry to see you going.',
                'icon' => '',
                'type' => 1,
            ];

            sendSingleAppNotification($CappData, env('CUS_FCM'));
            sendSingleAppNotification($VappData, env('VEN_FCM'));
            if($order->delivered_by){
                sendAppNotification($appData, env('DEL_FCM'));
            }
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Order canceled', 'order_id' => $order->id, 'order_refferel' => $order->prefix.$order->id]);
        } catch (\Throwable $th) {
            DB::rollback();
            return apiCatchResponse();
            dd($th);
        }
    }

    public function cancelReason(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required',
                'reason' => 'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            $order = \App\Order::find($request->order_id)->update(['cancel_reason' => $request->reason]);
            return response()->json(['status' => true, 'message' => 'Reason submitted']);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            //throw $th;
        }
    }

    public function orderAgain(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }

            DB::beginTransaction();
            if(Auth::user()->cart()){
                Auth::user()->cart()->delete();
            }
            $order = \App\Order::where('id', $request->order_id)->first();
            $newCart = $order->cart->replicate();
            $newCart->checkout = 0;
            $newCart->coupon_id = null;
            $newCart->coupon_details = null;
            $newCart->save();

            foreach ($order->cart->cartProduct as $key => $value) {
                $new = $value->replicate();
                $new->cart_id = $newCart->id;
                $new->save();
            }
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Products Added to Cart']);
        } catch (\Throwable $th) {
            DB::rollback();
            return apiCatchResponse();
            dd($th);
        }
    }

    public function submitRating(Request $request){
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'rating' => 'required',
            // 'comment' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response()->json(['status' => false, 'message' => $errors]);
        }
        $order = \App\Order::where('id', $request->order_id)->first();
        if($order->delivered_by && $order->order_status == 3){
            $review = new \App\Review;
            $review->storeReviewForDelivery($order->delivered_by, $order->id, $request->rating, $request->comment);
            return response()->json(['status' => true, 'message' => 'Rating Submitted']);
        }
        return response(['status' => false, 'message' => 'Something Went Wrong!']);
    }
}