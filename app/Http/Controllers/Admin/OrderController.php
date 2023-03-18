<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Order;
use Illuminate\Http\Request;
use Validator, Auth, DB, Session;
use App\Authorizable;
use App\Notifications\DeliveryNotification;
use Notification;

class OrderController extends Controller
{
    use Authorizable;
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
                $data['orders'] = Order::with('shop', 'user', 'deliveredBy')->where('confirmed_at', '!=', null)->where('pay_status', 0)->orderBy('expected_time', 'desc')->paginate(10);
            }
            if(auth()->user()->hasAnyRole('vendor')){
                $shop_id = Auth::user()->shop->id;
                $data['orders'] = \App\Shop::find($shop_id)->orders()->with('shop', 'user', 'deliveredBy')->where('confirmed_at', '!=', null)->where('pay_status', 0)->orderBy('expected_time', 'desc')->paginate(10);
                $data['shop'] = Auth::user()->shop;
            }
            if (request()->ajax()) {
                if(isset(request()->search) && !empty(request()->search)){
                    if (auth()->user()->hasAnyRole('admin')) {
                        $data['orders'] = Order::with('shop', 'user', 'deliveredBy')->
                        where('amount', 'Like', '%'.request()->search.'%')
                                    ->orWhere('search', 'Like','%'.request()->search.'%')
                                    ->orWhereHas('user', function($query){
                                        $query->where('name', 'Like', '%'.request()->search.'%')
                                    ->orWhere('mobile', 'Like', '%'.request()->search.'%');})
                                    ->orWhereHas('shop', function($query){
                                        $query->where('name', 'Like', '%'.request()->search.'%');})
                                    ->where('confirmed_at', '!=', null)->where('pay_status', 0)
                                            ->orderby('created_at','desc')->paginate(10);
                    }else{
                        $data['orders'] = Order::with('shop', 'user', 'deliveredBy')->where('search', 'Like', '%'.request()->search.'%')->where('shop_id', $shop_id)->where('confirmed_at', '!=', null)
                        ->where('pay_status', 0)->paginate(10);
                    }

                }
//                Session::put(['prev_page_no' => $data['shops']->currentPage()]);
                return view('order.order_table', array('orders' => $data['orders']))->render();
            }
            $data['orders_count'] = $data['orders']->total();
            return view('order.orders', $data);
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
            return view('order.order_detail', $data ?? NULL);
        } catch (\Throwable $th) {
        //    return catchResponse();
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

    public function filter($shop, $type){
        try {
            if($shop === 'all' && $type == 'all' ){
            return redirect(route('orders.index'));
        }
      
            $data['shops'] = \App\Shop::all();
            if($type == 'all' && $shop == 'all'){
                $data['orders'] =\App\Order::where('confirmed_at', '!=', null)->where('pay_status', 0)->paginate(10);
            }
            if($shop == 'all' && $type != 'all'){
                $data['orders'] = \App\Order::where('order_status', $type)->where('confirmed_at', '!=', null)->where('pay_status', 0)->paginate(10);
            }
            if($shop != 'all'){
                $data['orders'] = \App\Shop::find($shop)->orders()->where('confirmed_at', '!=', null)->where('pay_status', 0)->paginate(10);
            }
            if($shop != 'all' && $type != 'all'){
                $data['orders'] = \App\Shop::find($shop)->orders()->where('confirmed_at', '!=', null)->where('pay_status', 0)
                ->where('order_status', $type)->paginate(10);
            }
            
        

            $data['shop'] = Auth::user()->shop;

        
        if (request()->ajax()) {
            return view('order.order_table', array('orders' => $data['orders']))->render();
        }
        $data['orders_count'] = $data['orders']->total();
        return view('order.orders', $data ?? null);
        } catch (\Throwable $th) {
            return catchResponse();
        }
    }

    public function manualDelivery(Request $request){
        try {
             $order = \App\Order::where('id', $request->id)->where('confirmed_at', '!=', null)->where('canceled_at', null)->first();
             $order->order_status = 3;
             $order->paid = 1;
             $order->picked_at = $order->picked_at ?? null;
             $order->delivered_at = now();
             if($order->save()){
                $VappData = [
                    'fcm' => $order->shop->user->fcm,
                    'title' => config('constants.order_delivered.title'),
                    'body' => 'VDeliverz - Delivery, '.$order->search.', is delivered to the '.$order->user->name.' by Manually at '.now()->format('d-m-Y H:i'),
                    'icon' => '',
                    'type' => 1,
                ];
                $CappData = [
                    'fcm' => $order->user->fcm,
                    'title' => config('constants.order_delivered.title'),
                    'body' => 'VDeliverz Delivery, Hurray ! You received your order, Thanks for shopping with VDeliverz. Shop more and avail more coupons.',
                    'icon' => '',
                    'type' => 1,
                ];

                sendSingleAppNotification($CappData, env('CUS_FCM'));
                sendSingleAppNotification($VappData, env('VEN_FCM'));
                return true;
             }
             return false;
        } catch (\Throwable $th) {
            return catchResponse();
            dd($th);
        }
    }

    public function reviewOrder(Request $request){
        try {
            $order = \App\Order::find($request->order_id);
            $order->order_status = $request->action;
            $users = \App\User::role('delivery-boy')->where('active', 1)->get();
            if ($request->action == 1){
                $order->accepted_at = now();
                $order->assigned_at = now();
                $appData = [
                    'users' => \App\User::role('delivery-boy')->where('fcm', '!=', null)->get(),
                    'title' => 'New Order Arrived.',
                    'body' => 'Order refferal : '.$order->search,
                    'icon' => '',
                    'type' => 1
                ];
                sendAppNotification($appData, env('DEL_FCM'));
                Notification::send($users, new DeliveryNotification($order));
            }
            if($request->action == 8){
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
                Notification::send($users, new DeliveryNotification($order));
            }
            if($request->action == 6){
                $order->user->notifications()->where('data->order_id', $order->id)->update(['read_at' => null]);
                $order->rejected_at = now();
            }
            if($request->action == 5){
                $order->accepted_at = now();
                $order->user->notifications()->where('data->order_id', $order->id)->update(['read_at' => null]);
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
            flash()->info('Order status changed.');
            return back();
        }catch (\Throwable $th){
            return catchResponse();
            dd($th);
        }
    }

    public function showReview(Request $request){
        // 0 => Order Canceled by Customer
        // 1 => Order accepted and assigned
        // 2 => Order out for delivery
        // 3 => Order delivered
        // 4 => Order picked by delivery boy
        // 5 => Order accepted by vendor
        // 6 => Order rejected by vendor
        // 7 => Order confirmed
        $data['order'] = \App\Order::find($request->id);
        return view('order.dynamic', array('order' => $data['order']))->render();
    }
    
     public function changeOrderStatus(Request $request)
    {
        $order = \App\Order::where('id',$request->order_id)->first(); 
        $order->order_status = $request->order_status;
        $order->save();
        return $order;
    }
    
    public function delete_orders(Request $request)
    {
        $order = \App\Order::where('id',$request->order_id)->first(); 
        $order->delete();
        return back();
    }
     public function removeCartProduct(Request $request)
    {
        $cart_product_id = \App\CartProduct::find($request->cart_product_id);
        $isDeleted = $cart_product_id->delete();
        if($isDeleted > 0)
        {
            $cart = \App\Cart::find($request->cart_id);
            $cart->total_amount = $cart->total_amount - $cart_product_id->amount;
            $cart->save();
            
            $order = \App\Order::where('cart_id',$request->cart_id)->first(); 
            $order->amount = $order->amount - $cart_product_id->amount;
            $order->save();
        }
       // dd($cart_product_id->amount,$request->cart_id);
    }
    
    public function getvariantOfProduct(Request $request)
    {
        $stock_variants = \App\Stock::where('product_id',$request->product_id)->get();
        $html = '<option value="0">Select Variants</option>';
        foreach ($stock_variants as $stock_variant) {
           //dd($stock_variant->id,$stock_variant->variant);
           $html .= '<option value="'.$stock_variant->id.'">'.$stock_variant->variant .$stock_variant->unit.'</option>';
        }
        
        return response()->json(['html' => $html]);
    }
    
    public function getActualPriceOfProduct(Request $request)
    {
        //dd(12);
        $stock_variants = \App\Stock::where('id',$request->stock_id)->first();//dd($stock_variants->price);
        return response()->json($stock_variants->price);
    }
    
    public function addToCart(Request $request)
    {
        
          DB::beginTransaction();
            $id = $request->stock_id;
            //$device_id = $request->device_id;
            $quantity = $request->quantity;
            $stock = \App\Stock::find($id);
           
           
            $stock->available -= $quantity;
            
               
                  
                $cart = \App\Cart::where('id', $request->cart_id)->first();
         
                $cart->products_count += $quantity;
               
                $count = $cart->products_count;
             
                //$pro_datas = \App\CartProduct::where('stock_id', $id)->get(); dd($pro_datas);
                 $found = \App\CartProduct::where('cart_id', $request->cart_id)->first();
                //$found = false;
                $is_top = false;$toppings = []; 
              
               // dd($found);
                if($found){
                  
                    $cart->coupon_amount = 0;
                    $cart->coupon_id = null;
                    $cart->coupon_details = null;
                    
                    $cart->vendorcoupon_amount = 0;
                    $cart->vendorcoupon_id = null;
                    $cart->vendorcoupon_details = null;
                    $cart->total_amount = $cart->total_amount + ($stock->price * $quantity);
                    $cart->save();
                    
                    $order = \App\Order::where('cart_id',$request->cart_id)->first(); 
                    $order->amount = $order->amount + ($stock->price * $quantity);
                    $order->save();
                
                   // dd($quantity, round($stock->price + 0, 2),$stock->id,json_encode($stock),json_encode($stock->product),json_encode($toppings));
                    $cart->cartProduct()->save(new \App\cartProduct(['count' => $quantity, 'amount' => round($stock->price * $quantity, 2), 'stock_id' => $stock->id, 'stock_details' => json_encode($stock),
                    'product_details' => json_encode($stock->product), 'toppings' => json_encode($toppings), 'toppings_total' => 0]));
                }

            $stock->save();
            DB::commit();
    }
}
