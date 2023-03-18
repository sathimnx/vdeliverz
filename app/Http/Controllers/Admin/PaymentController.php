<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Shop;
use App\Payment;
use Session, Validator, DB, Auth;

class PaymentController extends Controller
{
    public function index(){
        if(Auth::user()->hasAnyRole('admin')){
        $data['shops'] = Shop::paginate(10);
        }elseif(Auth::user()->hasAnyRole('vendor')){
            $data['shops'] = Shop::where('user_id', Auth::user()->id)->paginate(10);
        }else{
            abort(404);
        }
        if (request()->ajax()) {
            return view('payments.sales_table', array('shops' => $data['shops']))->render();
        }
        return view('payments.sales', $data);
    }

    public function show(Shop $shop, $status){
        if(auth()->user()->hasAnyRole('vendor') && auth()->user()->shop->id != $shop->id){
           abort(404);
        }
        $data['banks'] = \App\Bank::where('shop_id', $shop->id)->get();
        $data['shop'] = $shop;
        $data['orders'] = \App\Order::where('shop_id', $shop->id)->where('order_status', 3)->where('pay_status', $status)->get();
        // if($data['orders']->count() <= 0){
        //     flash('No pending Payments');
        //     return back();
        //  }
        // $payment = Payment::where('shop_id', $shop->id)->first() ?? new Payment;
        // if (!Payment::where('shop_id', $shop->id)->first()) {
        //     $payment->shop_id = $shop->id;
        //     $payment->order_ids = "NILL";
        //     foreach ($data['orders'] as $key => $order) {
        //         $payment->tot_amt += $order->cart->total_amount;
        //         $payment->tot_charge += $order->cart->delivery_charge;
        //         $payment->tot_com += $order->comission;
        //         $payment->amt_paid = 0;
        //         $payment->save();
        //     }
        // }

        // $data['payment'] = $payment;
        $data['total_orders'] = $data['orders'] ->count();
        $data['shop_total'] = $data['orders']->sum(function($item){
            return str_replace(',', '', $item->cart->total_amount) - str_replace(',', '', $item->comission);
        });
        $data['commission'] = $data['orders']->sum('comission');

        return view('payments.sales_detail', $data);
    }

    public function payment(Request $request, $shop){
        // dd($request);
        $data['orders'] = \App\Order::where('shop_id', $shop)->where('order_status', 3)->where('pay_status', 0)->get();
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
        // 998.4
// 357.6
// 125
// dd($tot_amount, $delivery_charge, $commission);
        $createRequest = \App\Request::create([
            'shop_id' => $shop,
            'pay_status' => $request->action ?? 1,
            'requested_at' => $request->action == 1 ? now() : now(),
            'accepted_at' => $request->action == 2 ? now() : null,
            'completed_at' => $request->action == 3 ? now() : null,
            'request_ins' => $request->action == 1 ? $request->instruction : 'Direct payment by VDeliverz.',
            'accept_ins' => $request->action == 2 ? $request->instruction : null,
            'complete_ins' => $request->action == 3 ? $request->instruction : null,
            'order_ids' => $order_ids,
            'count' => $data['orders']->count(),
            'total' => $tot_amount,
            'com' => $commission,
            'charge' => $delivery_charge,
            'bank_id' => $request->bank
        ]);
        DB::commit();
        return redirect()->route('payments.index');
    }

    public function paymentHistory($shop, $status){
        if($shop == 'all' && $status == 'all'){
            return redirect()->route('payments.index');
        }
        if(auth()->user()->hasAnyRole('admin')){
            $data['shops'] = \App\Shop::all();
            if($status == 'all'){
                $data['payments'] = \App\Request::where('shop_id', $shop)->paginate(10);
            }
            elseif($shop == 'all'){
                $data['payments'] = \App\Request::where('pay_status', $status)->paginate(10);
            }else{
                $data['payments'] = \App\Request::where('shop_id', $shop)->where('pay_status', $status)->paginate(10);
            }

        }elseif(auth()->user()->hasAnyRole('vendor') && auth()->user()->shop->id == $shop){
            if($status == 'all'){
                $data['payments'] = \App\Request::where('shop_id', auth()->user()->shop->id)->paginate(10);
            }
            else{
                $data['payments'] = \App\Request::where('shop_id', $shop)->where('pay_status', $status)->paginate(10);
            }
        }else{
            abort(404);
        }
        if (request()->ajax()) {
            return view('history.sales_table', array('payments' => $data['payments']))->render();
        }

        return view('history.sales', $data);
    }
    public function paymentHistoryList(){

        if(auth()->user()->hasAnyRole('admin')){
            $data['payments'] = \App\Request::with('shop')->paginate(10);
            $data['shops'] = \App\Shop::all();
        }elseif(auth()->user()->hasAnyRole('vendor')){
            $data['payments'] = \App\Request::where('shop_id', auth()->user()->shop->id)->paginate(10);
        }else{
            abort(404);
        }
        if (request()->ajax()) {
            return view('history.sales_table', array('payments' => $data['payments']))->render();
        }
        return view('history.sales', $data);
    }

    public function historyView($id){
        $data['payment'] = \App\Request::find($id);
        $data['shop'] = Shop::find($data['payment']->shop_id);
        $order_ids = explode(',', $data['payment']->order_ids);
        $data['orders'] = \App\Order::whereIn("id", $order_ids)->get();
        return view('history.sales_detail', $data);
    }

    public function paymentResponse(Request $request, $id){
        $paymentRequest = \App\Request::find($id);
        if($request->action == 2){
            $paymentRequest->accept_ins = $request->instruction;
            $paymentRequest->accepted_at = now();
        }
        if($request->action == 3){
            $paymentRequest->complete_ins = $request->instruction;
            $paymentRequest->completed_at = now();
            $paymentRequest->completed_by = auth()->id();
        }
        $paymentRequest->pay_status = $request->action;
        $paymentRequest->save();
        return redirect()->route('payments.index');
    }
}