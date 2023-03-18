<?php

namespace App\Http\Controllers\Admin;

use App\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PaidOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            if(auth()->user()->hasAnyRole('admin')){
                $data['shops'] = \App\Shop::all();
                $data['orders'] = Order::with('shop', 'user', 'deliveredBy')->where('confirmed_at', '!=', null)->where('pay_status', 1)->latest()->paginate(10);
            }
            if(auth()->user()->hasAnyRole('vendor')){
                $shop_id = Auth::user()->shop->id;
                $data['orders'] = \App\Shop::find($shop_id)->orders()->with('shop', 'user', 'deliveredBy')->where('confirmed_at', '!=', null)->where('pay_status', 1)->latest()->paginate(10);
                $data['shop'] = Auth::user()->shop;
            }
            if (request()->ajax()) {
                if(isset(request()->search) && !empty(request()->search)){
                    if (auth()->user()->hasAnyRole('admin')) {
                        $data['orders'] = Order::with('shop', 'user', 'deliveredBy')->where('search', 'Like', '%'.request()->search.'%')->where('confirmed_at', '!=', null)->where('pay_status', 1)
                                            ->latest()->paginate(10);
                    }else{
                        $data['orders'] = Order::with('shop', 'user', 'deliveredBy')->where('search', 'Like', '%'.request()->search.'%')->where('shop_id', $shop_id)->where('confirmed_at', '!=', null)->where('pay_status', 1)
                                            ->latest()->paginate(10);
                    }
                }
//              Session::put(['prev_page_no' => $data['shops']->currentPage()]);
                return view('paid_orders.order_table', array('orders' => $data['orders']))->render();
            }
            $data['orders_count'] = $data['orders']->total();
            return view('paid_orders.orders', $data);
        } catch (\Throwable $th) {
            return catchResponse();
            // dd($th);
            //throw $th;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        try {
            $data['order'] = $order;
            $notify = auth()->user()->unreadNotifications()->where('data->admin_order_id', $order->id)->first();
            $notify ? $notify->markAsRead() : null;
            if(auth()->user()->hasAnyRole('vendor')){
                if(Auth::user()->shop->id != $order->cart->shop_id){
                    abort(404);
                }
            }
            return view('paid_orders.order_detail', $data ?? NULL);
        } catch (\Throwable $th) {
           return catchResponse();
            dd($th);
            abort(404);

        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }

    public function filter($shop, $type = 3){
        try {
            if($shop === 'all' && $type == 'all'){
            return redirect(route('orders.index'));
        }
        if(Auth::user()->hasAnyRole('admin')){
            $data['shops'] = \App\Shop::all();
            if($shop == 'all'){
                $data['orders'] = \App\Order::with('shop', 'user', 'deliveredBy')->where('order_status', $type)->where('confirmed_at', '!=', null)->where('pay_status', 1)->paginate(10);
            }
            if($shop != 'all'){
                $data['orders'] = \App\Shop::find($shop)->orders()->with('shop', 'user', 'deliveredBy')->where('confirmed_at', '!=', null)->where('pay_status', 1)->where('order_status', $type)->paginate(10);
            }
            if($type == 'all'){
                $data['orders'] = \App\Shop::find($shop)->orders()->with('shop', 'user', 'deliveredBy')->where('confirmed_at', '!=', null)->where('pay_status', 1)->paginate(10);
            }
        }
        if(auth()->user()->hasAnyRole('vendor')){
            if( Auth::user()->shop->id != $shop){
                abort(404);
            }
            if($type != 'all'){
                $data['orders'] = \App\Shop::find($shop)->orders()->where('confirmed_at', '!=', null)->where('pay_status', 1)->where('order_status', $type)->paginate(10);
            }
            if($type == 'all'){
                $data['orders'] = \App\Shop::find($shop)->orders()->where('confirmed_at', '!=', null)->where('pay_status', 1)->paginate(10);
            }
            $data['shop'] = Auth::user()->shop;

        }
        if (request()->ajax()) {
            return view('paid_orders.order_table', array('orders' => $data['orders']))->render();
        }
        $data['orders_count'] = $data['orders']->total();
        return view('paid_orders.orders', $data);
        } catch (\Throwable $th) {
            return catchResponse();
            dd($th);
        }
    }
}