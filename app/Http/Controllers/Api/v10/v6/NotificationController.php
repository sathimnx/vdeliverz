<?php

namespace App\Http\Controllers\Api\v6;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB, Auth, Validator;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function notifications(){
        try {
            $user = Auth::user();
            $data = [];
            foreach ($user->unreadNotifications as $key => $value) {
                if(isset($value->data['product_id'])){
                    $stock = \App\Stock::where('product_id', $value->data['product_id'])->first();
                    $notify = [
                        "notify_id" => $value->notifiable_id,
                        "notify_head" => $value->data['product_name'] ?? NULL,
                        'description' => $value->data['description'] ?? NULL,
                        'image' => $stock->product->image ?? NULL,
                        'time' => Carbon::createFromTimeStamp(strtotime($value->created_at))->diffForHumans()
                        ];
                        array_push($data, $notify);
                }
                if(isset($value->data['order_id'])){
                    $order = \App\Order::where('id', $value->data['order_id'])->where('canceled_at', null)->first();
                    if($order && $order->order_status == 2){
                        $notify = [
                            "notify_id" => $value->notifiable_id,
                            "notify_head" => $order->shop->name,
                            'image' => env('APP_URL').config('constants.notify').'cancel.png',
                            'description' => 'Delivery champ has accepted.',
                            'time' => Carbon::createFromTimeStamp(strtotime($value->created_at))->diffForHumans()
                        ];
                        array_push($data, $notify);
                    }
                    if($order && $order->order_status == 3){
                            $notify = [
                            "notify_id" => $value->notifiable_id,
                            "notify_head" => $order->shop->name,
                            'image' => env('APP_URL').config('constants.notify').'delivered.png',
                            'description' => 'Your order deliveried Succesfully',
                            'time' => Carbon::createFromTimeStamp(strtotime($value->created_at))->diffForHumans()
                        ];
                        array_push($data, $notify);
                    }
                    if($order && ($order->order_status == 1 || $order->order_status == 7 || $order->order_status == 8)){
                        $notify = [
                            "notify_id" => $value->notifiable_id,
                            "notify_head" => $order->shop->name,
                            'image' => env('APP_URL').config('constants.notify').'placed.png',
                            'description' => 'Order Created.',
                            'time' => Carbon::createFromTimeStamp(strtotime($value->created_at))->diffForHumans()
                        ];
                        array_push($data, $notify);
                    }
                    if($order && $order->order_status == 5 || $order && $order->order_status == 9){
                        $notify = [
                            "notify_id" => $value->notifiable_id,
                            "notify_head" => $order->shop->name,
                            'image' => env('APP_URL').config('constants.notify').'placed.png',
                            'description' => 'Order Confirmed.',
                            'time' => Carbon::createFromTimeStamp(strtotime($value->created_at))->diffForHumans()
                        ];
                        
                        array_push($data, $notify);

                     }
                    if($order && $order->order_status == 6){
                        $notify = [
                            "notify_id" => $value->notifiable_id,
                            "notify_head" => $order->shop->name,
                            'image' => env('APP_URL').config('constants.notify').'cancel.png',
                            'description' => 'Order rejected by shop vendor',
                            'time' => Carbon::createFromTimeStamp(strtotime($value->created_at))->diffForHumans()
                        ];
                        array_push($data, $notify);
                    }
                    if($order && $order->order_status == 0){
                        $notify = [
                            "notify_id" => $value->notifiable_id,
                            "notify_head" => $order->shop->name,
                            'image' => env('APP_URL').config('constants.notify').'cancel.png',
                            'description' => 'Order Cancelled.',
                            'time' => Carbon::createFromTimeStamp(strtotime($value->created_at))->diffForHumans()
                        ];
                        array_push($data, $notify);
                    }
                }

            }
            $user->unreadNotifications->markAsRead();
            return response()->json(['status' => true, 'message' => 'Notification Data', 'notification_list' => $data]);
        } catch (\Throwable $th) {
            //return apiCatchResponse();
            dd($th);
        }
    }
}
