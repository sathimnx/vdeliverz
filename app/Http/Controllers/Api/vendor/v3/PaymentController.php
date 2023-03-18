<?php

namespace App\Http\Controllers\Api\vendor\v3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, DB, Auth;
use App\Http\Resources\PaymentResource;

class PaymentController extends Controller
{
    public function index(Request $request){
        $validator = Validator::make($request->all(), [
            'shop_id' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $shop_id = $request->shop_id;
        $data['status'] = true;
        $data['message'] = 'Payment Withdrawal Requests';
        $data['currency'] = '₹';
        $payments = \App\Request::where('shop_id', $shop_id)->paginate(10);
        $data['Withdrawals'] = PaymentResource::collection($payments);
        $data['pagination'] = apiPagination($payments);
        $orders = \App\Order::where('shop_id', $shop_id)->where('order_status', 3)->where('pay_status', 0)->get();
        $data['total_orders'] = $orders->count();
        $data['total_earning'] = $orders->sum(function($item){
            return str_replace(',', '', $item->cart->total_amount);
        });
        $data['commission'] = number_format($orders->sum('comission'), 2);

        return response($data);
    }

    public function orderHistory(Request $request){
        $validator = Validator::make($request->all(), [
            'shop_id' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $shop_id = $request->shop_id;

        $orders = \App\Order::where('shop_id', $shop_id)->where('order_status', 3)->where('pay_status', 0)->paginate(10);
        foreach ($orders as $key => $value) {
            $order[] =  [
                            'order_id' => $value->id,
                            'referral' => $value->search,
                            'order_status' => $value->order_status,
                            'order_state' => $value->order_state,
                            'date' => $value->delivered_at->format('Y-m-d'),
                            'amount' => (str_replace(',', '', $value->cart->total_amount) - str_replace(',', '', $value->comission)).' '.config('constants.currency')
                        ];
        }
        $pagination = apiPagination($orders);
        return response(['status' => true, 'message' => 'Orders History', 'orders' => $order ?? null]);
    }

    public function requestWithdrawal(Request $request){
        $validator = Validator::make($request->all(), [
            'bank_id' => 'required',
            'shop_id' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $shop = $request->shop_id;
        $data['orders'] = \App\Order::where('shop_id', $shop)->where('order_status', 3)->where('pay_status', 0)->get();
        if($data['orders']->isEmpty()){
            return response(['status' => false, 'message' => 'No pending Payments.']);
        }
        $order_ids = implode(',', $data['orders']->pluck('id')->toArray());
        $tot_amount = 0;
        $delivery_charge = 0;
        $commission = 0;
        DB::beginTransaction();
        foreach ($data['orders'] as $key => $order) {
            $tot_amount += str_replace(',', '', $order->cart->total_amount) - str_replace(',', '', $order->comission);
            $delivery_charge += str_replace(',', '', $order->cart->delivery_charge);
            $commission += str_replace(',', '', $order->comission);
            $order->update(['pay_status' => 1]);
        }
        $createRequest = \App\Request::create([
            'shop_id' => $shop,
            'pay_status' => 1,
            'requested_at' => now(),
            'request_ins' => $request->instruction,
            'order_ids' => $order_ids,
            'count' => $data['orders']->count(),
            'total' => $tot_amount,
            'com' => $commission,
            'charge' => $delivery_charge,
            'bank_id' => $request->bank_id
        ]);
        DB::commit();
        return response(['status' => true, 'message' => 'Withdrawal Requested.', 'withdrawal_id' => $createRequest->id]);
    }

    public function withdrawalDetail(Request $request){
        $validator = Validator::make($request->all(), [
            'withdrawal_id' => 'required',
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $withdrawal = \App\Request::findOrFail($request->withdrawal_id);
        return response(['status' => true, 'message' => 'Withdrawal Detail.', 'withdrawal_detail' => new PaymentResource($withdrawal)]);
    }
}