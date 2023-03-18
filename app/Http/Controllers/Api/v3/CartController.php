<?php

namespace App\Http\Controllers\Api\v3;

use App\Cart;
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
                    $cart->save();
                }else{
                    $cart->total_amount = round($stock->price + $toppings_total, 2);
                    $cart->coupon_amount = 0;
                    $cart->coupon_id = null;
                    $cart->coupon_details = null;
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
                'address_id' => 'required'
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

                $shop_details = [
                    'cart_id' => $cart->id,
                    'shop_id' => $cart->shop->id,
                    'shop_name' => $cart->shop->name,
                    'shop_street' => $cart->shop->street,
                    'shop_image' => $cart->shop->image,
                    "is_opened" => $cart->shop->is_opened
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
                $cart->total_amount = round($tc, 2);
                $cart->products_count = $t_count;
                $tc <= 0 ? $cart->delete() : $cart->save();
                // $tc = str_replace(',', '', $cart->total_amount);
                $gst = $cart->gst_charge ? str_replace(',', '', $cart->gst_charge) : 0;
                $dc = $cart->delivery_charge ? str_replace(',', '', $cart->delivery_charge) : 0;
                $ca = $cart->coupon_amount ? str_replace(',', '', $cart->coupon_amount) : 0;
            return response()->json(['status' => true, 'message' => 'Cart Data', 'currency' => '₹', 'shop_details' => $shop_details,  'products' => $products, 'instructions' => $cart->instructions, 'total_items' => $total_items, 'coupon_discount' => number_format($ca, 2), 'delivery_charge' => number_format($dc, 2), 'tax' => 0,  'sub_total' => number_format($tc, 2), 'gst_charge' => number_format($gst,2), 'total' => number_format($tc + $dc - $ca, 2)]);
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
                'coupon_code' => 'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            $coupon_code = $request->coupon_code;
            DB::beginTransaction();

            if(auth('api')->user()){
                $cart = \App\Cart::where('user_id', auth('api')->user()->id)->where('checkout', 0)->first();
            }else{
                $cart = \App\Cart::where('device_id', $request->device_id)->where('checkout', 0)->first();
            }
            $coupon = \App\Coupon::whereRaw("BINARY `coupon_code`= ?",[$coupon_code])->whereDate('expired_on', '>=', now())
                        ->where('max_order_amount', '<=', $cart->total_amount)->first();
            if(auth('api')->user()){
                $already = \App\Cart::where('user_id', auth('api')->user()->id)->where('checkout', 1)->where('coupon_id', $coupon->id)->first();
            }
            if(!$coupon || $already){
                return response(['status' => false, 'message' => 'Coupon Not Available!']);
            }

            $cart->coupon_id = $coupon->id;
            $discount = round(( str_replace(',', '', $cart->total_amount) / 100 ) * $coupon->coupon_percentage, 2);
            $cart->coupon_amount = $discount;
            $discount_total = round(str_replace(',', '', $cart->total_amount) - $discount, 2);
            $cart->coupon_details = json_encode($coupon);
            $cart->save();
            DB::commit();
            return response(['status' => true, 'message' => 'Coupon Applied', 'coupon_discount' => $discount, 'total' => $discount_total]);

        } catch (\Throwable $th) {
            DB::rollback();
            return apiCatchResponse();
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
                'address_id' => 'required'
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

            return response()->json(['status' => true, 'message' => 'Slot Data', 'estimated_time' => $data['time'], 'slots' => $res_slots]);
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
