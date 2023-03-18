<?php

namespace App\Http\Controllers\Api\v3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth, DB, Validator;
use Carbon\Carbon;

class AccountController extends Controller
{

    public function user(){
        return response()->json(['status' => true, 'message' => 'User Data', 'user' => \App\User::where('id', Auth::user()->id)->first(['id', 'name', 'email', 'mobile'])]);
    }

    public function userUpdate(Request $request){
        $validator = Validator::make($request->all(), [
                'mobile' => 'required|unique:users,mobile,'.auth('api')->user()->id,
                'name' => 'required',
                "email" => "unique:users,email,".auth('api')->user()->id
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            $mobile = str_replace('+91', '', $request->mobile);
            $request->mobile = '+91'.$mobile;
            auth('api')->user()->update(['name' => $request->name, 'mobile' => $request->mobile, 'email' => $request->email]);
            return response()->json(['status' => true, 'message' => 'User Updated']);
    }

    public function addWishlist(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'shop_id' => 'required',
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            $wishlist = \App\Wishlist::where('user_id', Auth::user()->id)->where('shop_id', $request->shop_id)->first();
            if($wishlist){
                $wishlist->delete();
                return response()->json(['status' => true, 'message' => 'Shop deleted from Wishlist', 'is_wishlist' => false]);
            }else{
                \App\Wishlist::create(['user_id' => Auth::user()->id, 'shop_id' => $request->shop_id]);
                return response()->json(['status' => true, 'message' => 'Shop added to Wishlist', 'is_wishlist' => true]);
            }
            return response()->json(['status' => true, 'message' => 'Invalid shop', 'is_wishlist' => false]);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            dd($th);
        }
    }

    public function wishlists(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                // 'latitude' => 'required',
                // 'longitude' => 'required',
                'address_id' => 'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            if(!auth('api')->user()){
                return response(['status' => false, 'message' => 'Unauthorized!']);
            }
            $shops = auth('api')->user()->shops;
            $filter_datas = [];
            foreach ($shops as $key => $shop) {
                $data = canShowShop($request->address_id, $shop);
                $single = [
                'shop_id' => $shop->id,
                'shop_name' => $shop->name,
                'shop_image' => $shop->image,
                'shop_area' => $shop->area,
                'shop_address' => $shop->street.', '.$shop->area.', '.$shop->city,
                'price' => $shop->price.' '.$shop->currency,
                'int_dis' => $data['int_dis'],
                'distance' => $data['distance'],
                'time' => $data['time'],
                'rating' => $shop->rating_avg,
                'rating_count' => $shop->rating_count,
                'is_wishlist' => $shop->wishlist(),
                ];
                array_push($filter_datas, $single);
            }
            return response()->json(['status' => true, 'message' => 'Wishlist Datas', 'shops' => $filter_datas]);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            dd($th);
        }
    }

    public function myOrders(){
        try {
            $user = auth('api')->user();
            $total_orders = \App\Order::where('user_id', $user->id)->where('confirmed_at', "!=", null)->latest()->get();
            $orders = [];
            foreach ($total_orders as $key => $value) {
               $data = cartProducts($value->cart);
               $order = [
                   'order_id' => $value->id,
                   'order_referel' => $value->prefix.$value->id,
                   'order_status' => $value->order_status,
                   'shop_details' => $data['shop_details'],
                   'product_details' => productsList($data['products']),
                   'ordered_at' => $value->ordered_at,
                   'total_amount' => number_format($value->amount, 2),
                   'order_status' => $value->order_status,
                   'full_time_left' => $value->confirmed_at,
                  'time_left_min' => $value->time_left >= 1 ? 0 : $value->cancel_time['min'],
                   'time_left_sec' => $value->time_left >= 1 ? 0 : $value->cancel_time['sec'],
                   'can_cancel' => $value->time_left >= 1 ? false : $value->can_cancel,
                ];
                array_push($orders, $order);
            }
            return response()->json(['status' => true, 'message' => 'User Orders', 'orders' => $orders]);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            dd($th);
        }
    }

    public function trackOrder(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required',
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            $order = \App\Order::where('id', $request->order_id)->where('user_id', auth('api')->user()->id)->first();
          
            $data = cartProducts($order->cart);
            $address = json_decode($order->address);
            //$estimate = canShowShop($address->id, $order->shop);

            $order_details = [
                'order_id' => $order->id,
                'order_number' => $order->prefix.$order->id,
                'time_left_min' => $order->time_left >= 1 ? 0 : $order->cancel_time['min'],
                'time_left_sec' => $order->time_left >= 1 ? 0 : $order->cancel_time['sec'],
                'can_cancel' => $order->time_left >= 1 ? false : $order->can_cancel,
                'full_time_left' => $order->confirmed_at,
                'address' => $address->address,
                'payment_type' => $order->type,
                'items_total' => $data['total_items'],
                'sub_total' => number_format($data['sub_total'], 2),
                'tax' => $data['tax'],
                'delivery_charge' => number_format($data['delivery_charge'], 2),
                'coupon_discount' => number_format($data['coupon_discount'], 2),
                'total' => number_format($order->amount, 2),
                'estimated_time' => '45:10:24',
                'is_scheduled' => $order->cart->type,
            ];
            $time_details = [
                'order_status' => $order->order_status,
                'confirmed_at' => $order->ordered_at,
                'picked_at' => $order->taken_at,
                'delivered_at' => $order->completed_at,
            ];
            if($order->order_status == 4 || $order->order_status == 2 ){
                $delivery_boy = $order->deliveredBy->mobile;
            }
            
            return response()->json(['status' => true, 'message' => 'Track Order', 'shop_details' => $data['shop_details'], 'time_details' => $time_details, 'items' => $data['products'], 'order_details' => $order_details, 'delivery_boy_contact' => $delivery_boy ?? null ]);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            dd($th);
        }
    }

    //Logout
    public function logoutApi()
    {
        auth('api')->user()->AauthAcessToken()->where('name', 'customer_token')->delete();
        auth('api')->user()->update(['fcm' => null]);
        return response()->json(['status' => true, 'message' => 'Successfully Logged out']);
    }

    public function deliverylogoutApi()
    {
        auth('api')->user()->AauthAcessToken()->where('name', 'delivery_token')->delete();
        auth('api')->user()->update(['fcm' => null]);
        return response()->json(['status' => true, 'message' => 'Successfully Logged out']);
    }
}
