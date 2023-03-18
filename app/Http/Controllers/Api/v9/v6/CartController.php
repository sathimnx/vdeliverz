<?php

namespace App\Http\Controllers\Api\v6;

use App\Cart;
use App\Address;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth, DB, Validator;

class CartController extends Controller
{

    public function manageCart(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'device_id' => 'required',
                'quantity' => 'required|integer',
                'toppings' => 'array'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            DB::beginTransaction();
            $id = $request->id;
            $device_id = $request->device_id;
            $quantity = $request->quantity;
            $stock = \App\Stock::find($id);
            if(!$stock || $stock->available < $quantity){
                return response()->json(['status' => false, 'message' => 'Stock not available!', 'refresh' => false, 'quantity' => $count ?? 0]);
            }
            $stock->available -= $quantity;
            if(auth('api')->user()){
                $user_id = auth('api')->user()->id;
                $device_id = null;
                $cart = \App\Cart::where('user_id', $user_id)->where('checkout', 0)->first();
            }else{
                $user_id = null;
                $cart = \App\Cart::where('device_id', $device_id)->where('checkout', 0)->first();
            }
                if(isset($cart->shop->id) && $cart->shop->id != $stock->product->shop->id){
                    return response()->json(['status' => true, 'message' => 'Your cart contains items from '.$cart->shop->name.'. Do you want to clear the cartand add items from '.$stock->product->shop->name, 'refresh' => true]);
                }
                if(!$cart){
                    $cart = new \App\Cart;
                    // $cart->delivery_charge = $stock->product->shop->delivery_charge;
                    $cart->device_id = $device_id;
                    $cart->user_id = $user_id;
                    $cart->scheduled_at = now();
                    $cart->from = now();
                    $cart->to = now()->addMinutes(20);
                }
                $cart->shop_id = $stock->product->shop->id;
                $cart->products_count += $quantity;
                $cart->total_amount = 0;
                $count = $cart->products_count;
                if($cart->products_count <= 0){
                    $cart->delete();
                    $stock->save();
                    DB::commit();
                    return response()->json(['status' => true, 'message' => 'Cart Items Changed', 'refresh' => false, 'quantity' => $count ?? 0]);
                }
                $toppings = [];
                $toppings_total = 0;
                if (isset($request->toppings)) {
                    foreach ($request->toppings as $top){
                        $topping = \App\Topping::where('id', $top)->where('available', '>', 0)->first(['id', 'name', 'price', 'available']);
                        $toppings[] = $topping;
                        $toppings_total += $topping->price;
                        $topping->available -= 1;
                        $topping->save();
                    }
                }


                $pro_datas = $cart->cartProduct()->where('stock_id', $id)->get();
                // $found = \App\CartProduct::where('cart_id', $cart->id)->where('stock_id', $id)->first();
                $found = false;
                $is_top = false;
                if($pro_datas->isNotEmpty()){
                 foreach ($pro_datas as $pro){
                     $arr = array_map(function ($entry) {
                                return $entry['id'];
                            }, json_decode($pro->toppings, true));
                        if(isset($request->toppings) && $request->toppings === $arr){
                            $found = $pro;
                            $is_top = true;
                            break;
                        }
                    }
                }
                if($found == false && $request->toppings === null){
                    $found = \App\CartProduct::where('cart_id', $cart->id)->where('stock_id', $id)->first();
                }
                // dd($pro_datas, $found);
                if($found){
                    $found->count += $quantity;
                    if($found->count < 1){
                        $found->delete();
                        $stock->save();
                        DB::commit();
                        return response()->json(['status' => true, 'message' => 'Cart Items Changed', 'refresh' => false, 'quantity' => $count ?? 0]);
                    }
                    // $cc = str_replace(',', '', $found->amount);
                    // dd(($stock->price + $toppings_total + $cc) * $quantity);
                    $np = ($stock->price + $toppings_total) * $found->count;
                    $found->amount = round($np, 2);
                    $found->toppings_total = $toppings_total;
                    // resetToppings(json_decode($found->toppings, true));
                    $found->toppings = json_encode($toppings);
                    $found->save();
                    $cart->coupon_amount = 0;
                    $cart->coupon_id = null;
                    $cart->coupon_details = null;
                    $cart->save();
                }else{
                    // dd(number_format($stock->price + $toppings_total, 2));
                    $cart->coupon_amount = 0;
                    $cart->coupon_id = null;
                    $cart->coupon_details = null;
                    $cart->save();
                    $cart->cartProduct()->save(new \App\cartProduct(['count' => $quantity, 'amount' => round($stock->price + $toppings_total, 2), 'stock_id' => $stock->id, 'stock_details' => json_encode($stock), 'product_details' => json_encode($stock->product), 'toppings' => json_encode($toppings), 'toppings_total' => $toppings_total]));
                }

            $stock->save();
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Cart Items Changed', 'refresh' => false, 'quantity' => $count ?? 0]);
        } catch (\Throwable $th) {
            DB::rollback();
            // return apiCatchResponse();
            dd($th);
        }
    }
    public function refreshCartData(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'device_id' => 'required',
                'quantity' => 'required|integer',
                'toppings' => 'array'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            DB::beginTransaction();
            $id = $request->id;
            $device_id = $request->device_id;
            $quantity = $request->quantity;
            if(auth('api')->user()){
                $cart = \App\Cart::where('user_id', auth('api')->user()->id)->where('checkout', 0)->first();
                $cart->delete();
            }else{
                $cart = \App\Cart::where('device_id', $request->device_id)->where('checkout', 0)->first();
                $cart->delete();
            }
            $stock = \App\Stock::find($id);
            if(!$stock || $stock->available <= 0){
                return response()->json(['status' => false, 'message' => 'Stock not available!', 'refresh' => false, 'quantity' => $count ?? 0]);
            }
            $stock->available -= $quantity;
            if(auth('api')->user()){
                $user_id = auth('api')->user()->id;
                $device_id = null;
                $cart = \App\Cart::where('user_id', $user_id)->where('checkout', 0)->first();
            }else{
                $user_id = null;
                $cart = \App\Cart::where('device_id', $device_id)->where('checkout', 0)->first();
            }
                if(isset($cart->shop->id) && $cart->shop->id != $stock->product->shop->id){
                    return response()->json(['status' => true, 'message' => 'Your cart contains items from '.$cart->shop->name.'. Do you want to clear the cartand add items from '.$stock->product->shop->name, 'refresh' => true]);
                }
                if(!$cart){
                    $cart = new \App\Cart;
                    // $cart->delivery_charge = $stock->product->shop->delivery_charge;
                    $cart->device_id = $device_id;
                    $cart->user_id = $user_id;
                    $cart->scheduled_at = now();
                    $cart->from = now();
                    $cart->to = now()->addMinutes(20);
                }
                $cart->shop_id = $stock->product->shop->id;
                $cart->products_count += $quantity;
                $count = $cart->products_count;
                if($cart->products_count < 1){
                    $cart->delete();
                    $stock->save();
                    DB::commit();
                    return response()->json(['status' => true, 'message' => 'Cart Items Changed', 'refresh' => false, 'quantity' => $count ?? 0]);
                }
                $toppings = [];
                $toppings_total = 0;
                if (isset($request->toppings)) {

                    foreach ($request->toppings as $top){
                        $topping = \App\Topping::where('id', $top)->where('available', '>', 0)->first(['id', 'name', 'available', 'price']);
                        $toppings[] = $topping;
                        $toppings_total += $topping->price;
                        $topping->available -= 1;
                        $topping->save();
                    }
                }

                $found = $cart->cartProduct()->where('stock_id', $id)->first();
                if($found){
                    $found->count += $quantity;
                    if($found->count < 1){
                        $found->delete();
                        $stock->save();
                        DB::commit();
                        return response()->json(['status' => true, 'message' => 'Cart Items Changed', 'refresh' => false, 'quantity' => $count ?? 0]);
                    }

                    $found->amount += $stock->price * $quantity + $toppings_total;
                    $found->toppings_total = $toppings_total;
                    resetToppings(json_decode($found->toppings, true));
                    $found->toppings = json_encode($toppings);
                    $found->save();
                    $cart->total_amount = $cart->cartProduct()->sum('amount');
                    $cart->coupon_amount = 0;
                    $cart->coupon_id = null;
                    $cart->coupon_details = null;
                    
                     $cart->vendorcoupon_amount = 0;
                    $cart->vendorcoupon_id = null;
                    $cart->vendorcoupon_details = null;
                    $cart->save();
                }else{
                    $cart->total_amount = round($stock->price + $toppings_total, 2);
                    $cart->coupon_amount = 0;
                    $cart->coupon_id = null;
                    $cart->coupon_details = null;
                    
                    $cart->vendorcoupon_amount = 0;
                    $cart->vendorcoupon_id = null;
                    $cart->vendorcoupon_details = null;
                    $cart->save();
                    $cart->cartProduct()->save(new \App\cartProduct(['count' => $quantity, 'amount' => $stock->price + $toppings_total, 'stock_id' => $stock->id, 'stock_details' => json_encode($stock), 'product_details' => json_encode($stock->product), 'toppings' => json_encode($toppings), 'toppings_total' => $toppings_total]));
                }

            $stock->save();

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Cart Items Changed', 'refresh' => false, 'quantity' => $count ?? 0]);
        } catch (\Throwable $th) {
            DB::rollback();
            return apiCatchResponse();
            dd($th);
        }
    }

    public function getCartData(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'device_id' => 'required',
                // 'latitude' => 'required',
                // 'longitude' => 'required'
                'address_id' => 'required',
                
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            // dd(auth('api')->user());
         
            if(auth('api')->user()){
                $cart = \App\Cart::where('user_id', auth('api')->user()->id)->where('checkout', 0)->first();
            }else{
                $cart = \App\Cart::where('device_id', $request->device_id)->where('checkout', 0)->first();
            }
            if(!$cart){
                return response()->json(['status' => false, 'message' => 'No items in Cart']);
            }
            if($request->address_id){
                $data = canShowShop($request->address_id, $cart->shop);
                $cart->delivery_charge = calculateDeliveryCharge(ceil($data['int_dis']));
            }else{
                $cart->delivery_charge = $cart->shop->delivery_charge;
            }
            $Shop_opencloseDetails = get_shopopenclosedtl($cart->shop);  
               
             
                $shop_details = [
                    'cart_id' => $cart->id,
                    'shop_id' => $cart->shop->id,
                    'shop_name' => $cart->shop->name,
                    'shop_street' => $cart->shop->street,
                    'shop_image' => $cart->shop->image,
                    "is_opened" => $cart->shop->is_opened,
                    "shop_isopened" => $Shop_opencloseDetails['shopOpened'] =='' ? false : $Shop_opencloseDetails['shopOpened'],
                    "opening_time" => $Shop_opencloseDetails['from'],
                    "closing_time" => $Shop_opencloseDetails['to'],
                    "coupons" => $Shop_opencloseDetails['coupons'],
                    "shop_openclose_dtl"=> $Shop_opencloseDetails['Shop_openclose_dtl'],
                    "iscoloured_blue" =>$Shop_opencloseDetails['color']
                ];
                $products = [];
                $total_items = 0;
                $tc = 0;
                $t_count = 0;
                foreach ($cart->cartProduct as $key => $value) {
                    $tc += str_replace(',', '', $value->amount);
                    $t_count += $value->count;
                    $product = json_decode($value->product_details);
                    $stock = json_decode($value->stock_details);
                    $yes = 0;
                    if(\App\Product::find($product->id) && \App\Product::find($product->id)->toppings()->count() > 0){
                        $yes = 1;
                    }
                    $product = [
                        'id' => $stock->id,
                        'cart_product_id' => $value->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'variety' => $product->variety,
                        'size' => $stock->size ? $stock->size : $stock->variant.' '.$stock->unit,
                        'amount' => $stock->price,
                        'total_amount' => $value->amount,
                        'quantity' => $value->count,
                        'can_customize' => $yes,
                        'toppings' => json_decode($value->toppings)
                ];
                    array_push($products, $product);
                    $total_items += 1;
                }
                $ca = $cart->vendorcoupon_amount ? str_replace(',', '', $cart->vendorcoupon_amount) : 0;
                $cart->total_amount = round($tc, 2);
                //$cart->total_amount = round($tc, 2);
                $cart->products_count = $t_count;
                // $gst_charge = $cart->shop->gst == 1 ? calculateGSTCharge(str_replace(',', '', $cart->total_amount)) : 0;
                //$gst = $gst_charge ? str_replace(',', '', $gst_charge) : 0;
                $cart->grand_total = number_format($tc - $ca, 2);
                $tc <= 0 ? $cart->delete() : $cart->save();
                // $tc = str_replace(',', '', $cart->total_amount);
               
                //$gst = $cart->gst_charge ? str_replace(',', '', $cart->gst_charge) : 0;
                //$dc = $cart->delivery_charge ? str_replace(',', '', $cart->delivery_charge) : 0;
                //$ca = $cart->vendorcoupon_amount ? str_replace(',', '', $cart->vendorcoupon_amount) : 0;
            return response()->json(['status' => true, 'message' => 'Cart Data', 'currency' => '₹', 'shop_details' => $shop_details, 
            'products' => $products, 'instructions' => $cart->instructions, 'total_items' => $total_items, 'coupon_discount' => number_format($ca, 2), 
            'delivery_charge' => 0, 'tax' => 0,  'sub_total' => number_format($tc, 2), 'gst_charge' => 0, 'total' => number_format($tc, 2),
            'coupon_amount' => number_format($ca, 2), 'total_afterdiscount'=> number_format($tc  - $ca, 2)]);
        } catch (\Throwable $th) {
            // return apiCatchResponse();
            dd($th);
        }
    }

    //Coupons
    public function couponsList(){
        try {
            $coupons = \App\Coupon::where('active', 1)->whereDate('expired_on', '>=', now())->get(['id as coupon_id', 'coupon_code', 'coupon_percentage', 'max_order_amount', 'expired_on', 'coupon_description']);
            return response(['status' => true, 'message' => 'Coupons Found', 'coupons' => $coupons]);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            dd($th);
        }
    }

    //Apply Coupon
     public function applyCoupon(Request $request){
            try {
             $validator = Validator::make($request->all(), [
            'device_id' => 'required',
            'coupon_code' => 'required',
            'shop_id' =>'required',
            'product_ids' =>'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response()->json(['status' => false, 'message' => $errors]);
        }
      $coupon_code = $request->coupon_code;
        DB::beginTransaction();
       
        //dd(auth('api')->user());
        $user_id = $request->user_id;
       // dd($user_id,$request->shop_id);
    //$user_id = 688;
       //dd(\App\Cart::where('user_id', $user_id)->where('checkout', 0)->first());
        if($user_id){
            $cart = \App\Cart::where('user_id', $user_id)->where('checkout', 0)->first();
        }else{
            $cart = \App\Cart::where('device_id', $request->device_id)->where('checkout', 0)->first();
        }
        if(!$cart){
            return response()->json(['status' => false, 'message' => 'No items in Cart']);
        }
       
        $coupon = \App\VendorCoupon::where('coupon_code',$request->coupon_code)->whereDate('expired_on', '>=', now())->first();
 
        if(!$coupon){
            return response(['status' => false, 'message' => 'Coupon Not Available!']);
        }
        
         $CouponCount = 1;
        // dd($coupon->shop_id,$coupon->sub_category_id,$coupon->product_id,$coupon->category_id);
            if(($coupon->sub_category_id > 0 || $coupon->product_id > 0) || $coupon->category_id > 0)
            {
                if(\App\VendorCoupon::where('coupon_code',$request->coupon_code)->where('shop_id',0)->whereDate('expired_on', '>=', now())->count() > 0)
                {
                    $Shopcoupon = \App\VendorCoupon::where('coupon_code',$request->coupon_code)->where('shop_id',0)->whereDate('expired_on', '>=', now())->first();
                }
                else
                {
                    $Shopcoupon = \App\VendorCoupon::where('coupon_code',$request->coupon_code)->whereRaw("find_in_set($request->shop_id,shop_id)")->whereDate('expired_on', '>=', now())->first();
                }
                if(!$Shopcoupon){
                    return response(['status' => false, 'message' => 'Coupon not applicable for this shop!']);
                }
                
                     $cartData = \App\Cart::where('user_id', $user_id)->where('checkout', 0)->where('shop_id',$request->shop_id)
                             ->select('id')->latest()->first();
                           //  dd($cartData);
                    
                    $product_ids = explode(',',$request->product_ids);
                    $Choosed_products = \App\Product::whereIn('id', $product_ids)->get();   
                    $vendor_coupon = \App\VendorCoupon::where('coupon_code',$request->coupon_code)->whereDate('expired_on', '>=', now())->get();
                    
                    if($vendor_coupon[0]->product_id == 0)
                    {
                        foreach($Choosed_products as $products)
                        {
                            //dd($products->sub_category_id);
                            $vendor_subcategorys = explode(',',$vendor_coupon[0]->sub_category_id);
                            //dd($vendor_subcategorys[0]);
                            if($vendor_subcategorys[0] != '0'){
                                if(in_array($products->sub_category_id, $vendor_subcategorys) == false)
                                {
                                    $CouponCount = 0;
                                    return response(['status' => false, 'message' => 'This Coupon is not applicable for this Category Products!']);
                                }
                            }
                            else
                            {
                                $vendor_categorys = $products->category_id;
                                if($vendor_categorys != $vendor_coupon[0]->category_id)
                                {
                                    $CouponCount = 0;
                                    return response(['status' => false, 'message' => 'This Coupon is not applicable for this Category Products!']);
                                }
                                
                            }
                        }
                    }
                    else if(\App\VendorCoupon::where('coupon_code',$request->coupon_code)->whereIn('product_id', array($request->product_ids))->whereDate('expired_on', '>=', now())->count() == 0)
                    {
                        $CouponCount = 0;
                        return response(['status' => false, 'message' => 'This Coupon is not applicable for this Product!']);
                    }
                     
                    
            }
            //dd($CouponCount);
            $coupon = \App\VendorCoupon::where('coupon_code',$request->coupon_code)->whereDate('expired_on', '>=', now())->first();
            if($CouponCount > 0)
            {
                $CartCouponUsedCount = \App\Cart::where('user_id', $user_id)->where('vendorcoupon_id',$coupon->id)->count();
                $discount = 0; 
                if($CartCouponUsedCount <= $coupon->Discount_use_amt){
                    $cart->vendorcoupon_id = $coupon->id;
                    if($cart->total_amount >= $coupon->min_order_amt){
                        $CouponDiscount = round(( str_replace(',', '', $cart->total_amount) / 100 ) * $coupon->coupon_percentage, 2);
                        $discount = $CouponDiscount > $coupon->max_order_amount ? $coupon->max_order_amount : $CouponDiscount;
                        $cart->vendorcoupon_amount = $discount;
                        
                    }
                    else{
                        return response(['status' => false, 'message' => 'You order amount is not eligible to apply this order!']);
                    }
                }
                else{
                    return response(['status' => false, 'message' => 'Coupon already applied!']);
                }
            }
            else
            {
                return response(['status' => false, 'message' => 'Coupon is Invalid!']);
            }
        $discount_total = round(str_replace(',', '', $cart->total_amount) - $discount, 2);
        
        $cart->vendorcoupon_details = json_encode($coupon);
        $cart->save();
        DB::commit();
        return response(['status' => true, 'message' => 'Coupon Applied', 'coupon_discount' => $discount, 'total' => $discount_total]);
        } catch (\Throwable $th) {
           // DB::rollback();
           // return apiCatchResponse();
            dd($th);
        }
    }

    public function slot(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'shop_id' => 'required',
                // 'latitude' => 'required',
                // 'longitude' => 'required',
                'cart_id' => 'required',
                'address_id' => 'required',
                'product_ids'=>'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            $cart = \App\Cart::find($request->cart_id);
            if(str_replace(',', '', $cart->total_amount) < $cart->shop->min_amount){
                return response()->json(['status' => false, 'message' => 'Minimum Order Amount : '.$cart->shop->min_amount.' '.config('constants.currency')]);
            }
            $day = now()->format('l');
            $time = now()->format('H:i:s');
            $shop = \App\Shop::where('id', $request->shop_id)
            ->whereRaw("FIND_IN_SET('".$day."', weekdays)")
            ->where('opens_at', '<=', $time)
            ->where('closes_at', '>=', $time)
            ->where('opened', 1)->where('active', 1)->first();
            $data = canShowShop($request->address_id, $cart->shop);
            if(!$shop){
                return response()->json(['status' => false, 'message' => 'Shop currently not available']);
            }

            // $slots = \App\Slot::where('shop_id', $request->shop_id)->where('active', 1)->get();
            $slots = \App\Slot::where('shop_id', $request->shop_id)->whereRaw("FIND_IN_SET('".$day."', weekdays)")->where('from', '>=', $time)->where('active', 1)->get();
            // dd($slots);
            $res_slots = [];
            foreach ($slots as $key => $value) {
                $slot = [
                    'slot_id' => $value->id,
                    'time' => date('h:i a', strtotime($value->from)).'-'.date('h:i a', strtotime($value->to)),
                ];
                array_push($res_slots, $slot);
            }
            
            /* Product Availability Checking */

            $product_ids = explode(',',$request->product_ids);
           
            $products_notavailable = '';
            foreach ($product_ids as $product_id) {
                $product_timings = DB::table('product_opencloseTime')->where('product_id', $product_id)->get();
                $now = \Carbon\Carbon::now();
                $isavailable = 1;
                if(isset($product_timings) && isset($product_timings[0]))
                {
                    foreach ($product_timings as $key => $product_timing) {
                        
                        $start = \Carbon\Carbon::parse($product_timing->open_time)->toDatetimeString(); 
                        $end = \Carbon\Carbon::parse($product_timing->close_time)->toDatetimeString();
                        $products = \App\Product::where('id', $product_id)->get();
                        
                        if(\Carbon\Carbon::parse($start)->gt(\Carbon\Carbon::now()))
                        {
                            $products_notavailable = $products_notavailable != '' ? $products_notavailable . ',' . $products[0]->name : $products[0]->name;
                        }
                        elseif(\Carbon\Carbon::parse($start)->gt(\Carbon\Carbon::now()) == false)
                        {
                            $products_notavailable = $products_notavailable != '' ? $products_notavailable . ',' . $products[0]->name : $products[0]->name; 
                        }
                    }
                }
             }
             $products_availability = $products_notavailable !="" ? $products_notavailable . ' product is not available' : '';
            return response()->json(['status' => true, 'message' => 'Slot Data', 'estimated_time' => $data['time'], 'slots' => $res_slots,'products_availability' => $products_availability]);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            dd($th);
        }
    }

    public function bookSlot(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'cart_id' => 'required',
                'delivery_type' => 'required',
                'slot_id' => 'required_if:delivery_type,1'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            $slot = \App\Slot::find($request->slot_id);
            if($request->delivery_type == 1){
                $from = $slot->from;
                $to = $slot->to;
            }else{
                $from = now();
                $to = now()->addMinutes(20);
            }
            $cart = \App\Cart::find($request->cart_id);
            if(str_replace(',', '', $cart->total_amount) < $cart->shop->min_amount){
                return response()->json(['status' => false, 'message' => 'Add minimum amount : '.$cart->shop->min_amount.' '.config('constants.currency')]);
            }
            if(!$cart){
                return response()->json(['status' => false, 'message' => 'Invalid Cart']);
            }
            $cart->update(['scheduled_at' => now(), 'type' => $request->delivery_type, 'from' => $from, 'to' => $to]);
            return response()->json(['status' => true, 'message' => 'Slot booked']);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            dd($th);
        }
    }

    public function cartCustomize(Request $request){
        $validator = Validator::make($request->all(), [
            'cart_product_id' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response()->json(['status' => false, 'message' => $errors]);
        }
        $cart_product = \App\CartProduct::find($request->cart_product_id);
        $product = json_decode($cart_product->product_details);
        $product = \App\Product::find($product->id);
        $top_found = json_decode($cart_product->toppings, true);
        $stock_found = json_decode($cart_product->stock_details, true);
        $arr = array_map(function ($entry) {
            return $entry['id'];
        }, $top_found);
        $toppings = $product->toppings()->where('available', '>', 0)->whereHas('title', function ($query){
            $query->where('active', 1);
        })->get();
        $new_toppings = [];
        foreach ($toppings as $top){
            $new_toppings[] = ['topping_id' => $top->id,
                'name' => $top->name,
                'price' => number_format($top->price, 2),
                'variety' => $top->variety,
                'selected' => in_array($top->id, $arr)
            ];
        }
        $stocks = $product->stocks;
        $quantity = [];
        foreach ($stocks as $stock){
            $quantity[] = ['id' => $stock->id,
                'size' => $stock->size ?: $stock->variant . ' ' . $stock->unit,
                'variety' => $product->variety,
                'price' => number_format($stock->price, 2),
                'selected' => $stock_found['id'] == $stock->id
            ];
        }
        $p_name = ['product_name' => $product->name, 'variety' => $product->variety];
        return response(['status' => true, 'message' => 'Cart Toppings List', 'product' => $p_name, 'quantity' => $quantity, 'toppings' => $new_toppings]);
    }

    public function updateCartProduct(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'cart_product_id' => 'required',
                'toppings' => 'array'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            DB::beginTransaction();
            $cartProduct = \App\CartProduct::find($request->cart_product_id);
            $pro_datas = \App\CartProduct::where('cart_id', $cartProduct->cart_id)->where('stock_id', $request->id)->get();
            if($pro_datas->isNotEmpty()){
                $found = false;
                 foreach ($pro_datas as $pro){
                     $arr = array_map(function ($entry) {
                                return $entry['id'];
                            }, json_decode($pro->toppings, true));
                    // $arr1 = array_map(function ($entry1) {
                    //     return $entry1['id'];
                    // }, json_decode($request->toppings, true));
                        if($request->toppings === $arr && $pro->id != $cartProduct->id){
                            $found = $pro;
                            break;
                        }
                    }
                    if($found){
                        $this->updateCart($found->id, $cartProduct->count);
                        $cartProduct->delete();
                        DB::commit();
                        return response()->json(['status' => true, 'message' => 'Cart Items Changed', 'refresh' => false, 'quantity' => $count ?? 0]);
                    }
            }

            $oldPrice = $cartProduct->amount + $cartProduct->toppings_total;
            $id = $request->id;
            $stock = \App\Stock::find($id);
            if(!$stock || $stock->available < 1){
                return response()->json(['status' => false, 'message' => 'Stock not available!', 'refresh' => false, 'quantity' => $count ?? 0]);
            }
            $toppings = [];
            $toppings_total = 0;
            if (isset($request->toppings)) {
                foreach ($request->toppings as $top){
                    $topping = \App\Topping::where('id', $top)->where('available', '>', 0)->first(['id', 'name', 'price', 'available']);
                    $toppings[] = $topping;
                    $toppings_total += $topping->price;
                    $topping->available -= 1;
                    $topping->save();
                }
            }
            $cart = \App\Cart::find($cartProduct->cart_id);
            $sp = str_replace(',', '', $stock->price);
            $tt = str_replace(',', '', $toppings_total);
            $cartProduct->amount = round(($sp + $tt) * $cartProduct->count, 2);
            $cartProduct->toppings_total = $toppings_total;
            resetToppings(json_decode($cartProduct->toppings, true));
            $cartProduct->toppings = json_encode($toppings);
            $cartProduct->stock_id = $stock->id;
            $cartProduct->stock_details = json_encode($stock);
            $cartProduct->save();
            $cart->coupon_amount = 0;
            $cart->coupon_id = null;
            $cart->coupon_details = null;
            $cart->total_amount <= 0 ? $cart->delete() : $cart->save();
            $count = $cart->products_count;
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Cart Items Changed', 'refresh' => false, 'quantity' => $count ?? 0]);
        } catch (\Throwable $th) {
            DB::rollback();
            return apiCatchResponse();
            dd($th);
        }
    }

    public function addInstructions(Request $request){
        $validator = Validator::make($request->all(), [
            'cart_id' => 'required',
            'instructions' => 'required',
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response()->json(['status' => false, 'message' => $errors]);
        }
        $cart = \App\Cart::find($request->cart_id);
        $cart->instructions = $request->instructions;
        $cart->save();
        return response()->json(['status' => true, 'message' => 'instructions added.']);
    }

    public function manageCartProduct(Request $request){
        $validator = Validator::make($request->all(), [
            'cart_product_id' => 'required',
            'quantity' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response()->json(['status' => false, 'message' => $errors]);
        }
        DB::beginTransaction();
        $cartProduct = \App\CartProduct::find($request->cart_product_id);

        if(!$cartProduct){
            return response()->json(['status' => false, 'message' => 'Cart not available!', 'refresh' => false, 'quantity' => $count ?? 0]);
        }
        $stock = \App\Stock::find($cartProduct->stock_id);
        if(!$stock){
            $cartProduct->delete();
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Item Removed!', 'refresh' => false, 'quantity' => $count ?? 0]);

        }
        if($stock->available < 1){
            return response()->json(['status' => false, 'message' => 'Stock not available!', 'refresh' => false, 'quantity' => $count ?? 0]);
        }
        $arr =[];
        $arr = array_map(function ($entry) {
            return $entry['id'];
        }, json_decode($cartProduct->toppings, true) );
        if (count($arr) > 0) {
                    foreach ($arr as $top){
                        $topping = \App\Topping::where('id', $top)->first(['id', 'name', 'price', 'available']);
                        if($topping){
                            $topping->available -= $request->quantity;
                            $topping->save();
                        }
                    }
                }

        $cart = \App\Cart::find($cartProduct->cart_id);
        $cartProduct->count += $request->quantity;
        if($cartProduct->count < 1){
            $cartProduct->delete();
        }else{
            $sp = str_replace(',', '', $stock->price);
            $tt = str_replace(',', '', $cartProduct->toppings_total);
            $cartProduct->amount = round(($sp + $tt) * $cartProduct->count, 2);
            // $cartProduct->amount = number_format(($stock->price + $cartProduct->toppings_total) * $cartProduct->count, 2);
            $cartProduct->save();
        }
        // $cart->total_amount = $cart->cartProduct()->sum('amount');
        $cart->coupon_amount = 0;
        $cart->coupon_id = null;
        $cart->coupon_details = null;
        
        $cart->vendorcoupon_amount = 0;
        $cart->vendorcoupon_id = null;
        $cart->vendorcoupon_details = null;
        $stock->available -= $request->quantity;
        $stock->save();
        $cart->total_amount <= 0 ? $cart->delete() : $cart->save();
        $count = $cart->products_count;
        DB::commit();
        return response()->json(['status' => true, 'message' => 'Cart Items Changed', 'refresh' => false, 'quantity' => $count ?? 0]);
    }

    private function updateCart($cart_product_id, $quantity){
        $cartProduct = \App\CartProduct::find($cart_product_id);

        if(!$cartProduct){
            return response()->json(['status' => false, 'message' => 'Cart not available!', 'refresh' => false, 'quantity' => $count ?? 0]);
        }
        $arr =[];
        $arr = array_map(function ($entry) {
            return $entry['id'];
        }, json_decode($cartProduct->toppings, true));
        if (count($arr) > 0) {
                    foreach ($arr as $top){
                        $topping = \App\Topping::where('id', $top)->first(['id', 'name', 'price', 'available']);
                        $topping->available -= $quantity;
                        $topping->save();
                    }
                }
        $stock = \App\Stock::find($cartProduct->stock_id);
        if(!$stock || $stock->available < 1){
            return response()->json(['status' => false, 'message' => 'Stock not available!', 'refresh' => false, 'quantity' => $count ?? 0]);
        }
        $cart = \App\Cart::find($cartProduct->cart_id);
        $cartProduct->count += $quantity;
        if($cartProduct->count < 1){
            $cartProduct->delete();
        }else{
            $cartProduct->amount = round(($stock->price + $cartProduct->toppings_total) * $cartProduct->count, 2);
            $cartProduct->save();
        }
        $cart->total_amount = $cart->cartProduct()->sum('amount');
        $cart->coupon_amount = 0;
        $cart->coupon_id = null;
        $cart->coupon_details = null;
        $stock->available -= $quantity;
        $stock->save();
        $cart->total_amount <= 0 ? $cart->delete() : $cart->save();
        $count = $cart->products_count;
    }
    

}
