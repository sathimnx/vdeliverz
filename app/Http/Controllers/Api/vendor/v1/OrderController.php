<?php

namespace App\Http\Controllers\Api\vendor\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth, DB, Validator;
use Carbon\Carbon;
use App\Http\Resources\OrderDetailResource;
use App\Notifications\DeliveryNotification;
use Notification;

class OrderController extends Controller
{
    public function index(){
        // 0 => Order Canceled by Customer
        // 1 => Order accepted and assigned
        // 2 => Order yet to pick
        // 3 => Order delivered
        // 4 => Order picked by delivery boy
        // 5 => Order accepted by vendor
        // 6 => Order rejected by vendor
        // 7 => Confirmed & Not Assigned
        $orders = auth('api')->user()->vendorOrders()->where('confirmed_at', '!=', null)->orderBy('expected_time', 'desc')->paginate(8);

        foreach ($orders as $key => $value) {
            $date = $value->date == now()->format('Y-m-d') ? 'Today' : Carbon::parse($value->confirmed_at)->format('d M Y');
            $order[] =  [
                            'order_id' => $value->id,
                            'referral' => $value->search,
                            'order_status' => $value->order_status,
                            'order_state' => $value->order_state,
                            'date' => $date
                        ];
        }
        $pagination = apiPagination($orders);
        return response(['status' => true, 'message' => 'Orders List.', 'orders' => $order ?? [], 'pagination' => $pagination]);
    }

    public function orderReview(Request $request){

        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'action' => 'required|integer'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $order = \App\Order::find($request->order_id);
        $order->order_status = $request->action;
        switch ($request->action) {
            case 1:
                $order->accepted_at = $order->accepted_at ?? now();
                $order->assigned_at = now();
                $appData = [
                    'users' => \App\User::role('delivery-boy')->where('fcm', '!=', null)->get(),
                    'title' => 'New Order Arrived.',
                    'body' => 'Order refferal : '.$order->search,
                    'icon' => '',
                    'type' => 1
                ];
                sendAppNotification($appData, env('DEL_FCM'));
                Notification::send($appData['users'], new DeliveryNotification($order));
                break;
            case 8:
                $order->assigned_at = now();
                $order->order_status = 1;
                $appData = [
                    'users' => \App\User::role('delivery-boy')->where('fcm', '!=', null)->get(),
                    'title' => 'New Order Arrived.',
                    'body' => 'Order refferal : '.$order->search,
                    'icon' => '',
                    'type' => 1
                ];
                sendAppNotification($appData, env('DEL_FCM'));
                Notification::send($appData['users'], new DeliveryNotification($order));
                break;
            case 6:
                $order->rejected_at = now();
                break;
            case 5:
                $order->accepted_at = now();
                break;

            default:

                break;
        }

        $order->save();
        $VappData = [
            'fcm' => $order->shop->user->fcm,
            'title' => config('constants.order_status.title'),
            'body' => 'VDeliverz Order Status, Hey '.$order->shop->name.', You '.$order->order_state.' this Order '.$order->search,
            'icon' => '',
            'type' => 1
        ];
        $CappData = [
            'fcm' => $order->user->fcm,
            'title' => config('constants.order_status.title'),
            'body' => 'VDeliverz Order Status, Hey '.$order->user->name.', '.$order->shop->name.' '.$order->order_state.' Your Order '.$order->search.'. For More Details Track Here',
            'icon' => '',
            'type' => 1
        ];
        sendSingleAppNotification($VappData, env('VEN_FCM'));
        sendSingleAppNotification($CappData, env('CUS_FCM'));
        return response(['status' => true, 'message' => 'Order status changed.']);
    }

    public function orderDetails(Request $request){
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $order = \App\Order::find($request->order_id);

        return response(['status' => true, 'message' => 'Order Details.', 'order' => new OrderDetailResource($order)]);
    }

    public function search(Request $request){
        $validator = Validator::make($request->all(), [
            'search' => 'required',
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $orders = auth('api')->user()->vendorOrders()->where('confirmed_at', '!=', null)->where('search', 'Like','%'.$request->search.'%')->orderBy('expected_time', 'desc')->paginate(8);
        foreach ($orders as $key => $value) {
            $date = $value->date == now()->format('Y-m-d') ? 'Today' : Carbon::parse($value->confirmed_at)->format('d M Y');
            $order[] =  [
                            'order_id' => $value->id,
                            'referral' => $value->search,
                            'order_status' => $value->order_status,
                            'order_state' => $value->order_state,
                            'date' => $date
                        ];
        }
        $pagination = apiPagination($orders);
        return response(['status' => true, 'message' => 'Orders List.', 'orders' => $order ?? [], 'pagination' => $pagination]);

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