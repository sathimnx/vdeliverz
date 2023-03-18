<?php

use App\Charge;
use App\Address;
use App\AddressShop;
use Twilio\Rest\Client;
use Twilio\Jwt\ClientToken;

function catchResponse(){
    flash()->error('Something went wrong. Please try again!');
    return redirect()->back()->withInput();
}

function apiCatchResponse(){
    return response(['status' => false, 'message' => 'Something Went Wrong!']);
}
//Banners
function appBanners(){
    return \App\Banner::where('active', 1)->inRandomOrder()->limit(4)->get(['id', 'image']);
}

function sendPushNotification(array $data, $key)
{
    $data = json_encode($data);
    $url = 'https://fcm.googleapis.com/fcm/send';
    // $server_key = $key;

    $headers = array(
        'Content-Type:application/json',
        'Authorization:key='.$key
    );
    //CURL request to route notification to FCM connection server (provided by Google)
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $result = curl_exec($ch);
    $res = json_decode($result);
    // dd($res, $result);
    if ($res && $res->success == 0) {
        return $result;
    }
    curl_close($ch);
    return true;
}

function sendAppNotification($appData, $key){
    foreach ($appData['users'] as $k => $value) {
        $data = [
            'to' => $value->fcm,
            'data' => [
                "data" => [
                    "title" => $appData['title'],
                    "message" => $appData['body'],
                    "image" => "sample.png",
                    "type" => "1"
                ]
            ]
        ];
        sendPushNotification($data, $key);
    }

}
function sendSingleAppNotification($appData, $key){
    $data = [
        'to' => $appData['fcm'],
        'data' => [
            "data" => [
                "title" => $appData['title'],
                "message" => $appData['body'],
                "image" => "sample.png",
                "type" => "1"
            ]
        ]
    ];
    sendPushNotification($data, $key);
}



function apiPagination($data){
    $next = $data->currentPage() != $data->lastPage() ? $data->currentPage() + 1 : null;
    return [
        'total' => $data->total(),
        'count' => $data->count(),
        'per_page' => $data->perPage(),
        'current_page' => $data->currentPage(),
        'total_pages' => $data->lastPage(),
        "next_page_url" => $next ? $data->path()."?page=".$next : null,
    ];
}

function serviceProviders($providers, $request){
    $count = 0;
    foreach ($providers as $key => $value) {
        $data = scopeIsWithinMaxDistance($value->latitude, $value->longitude, $request->latitude, $request->longitude);
        if($data['int_dis'] <= $value->radius){
            $count += 1;
        }
    }
    $return = ['count' => $count];
    return $return;
}

function getOrderExpectedTime($time, $address_id, $shop){
    $data = canShowShop($address_id, $shop);
    $expected_time = \Carbon\Carbon::parse($time)->addMinutes($data['int_time'])->format('h:i a');
    $expected_date = \Carbon\Carbon::parse($time)->format('d-M-Y');
    return ['time' => $expected_time, 'date' => $expected_date];
}

function categoriesList(){
    return \App\Category::where('active', 1)->where('type_id', 1)->orderBy('order', 'asc')->get(['id', 'name', 'image', 'banner']);
}
function checkCartStock($id, $device_id){
    if(auth('api')->user()){
            $user_id = auth('api')->user()->id;
            $device_id = null;
            $cart = \App\Cart::where('user_id', $user_id)->where('checkout', 0)->first();
        }else{
            $user_id = null;
            $cart = \App\Cart::where('device_id', $device_id)->where('checkout', 0)->first();
        }

        $data['cart'] = 0;
        $data['count'] = 0;
        if(!$cart){
            return $data;
        }
        $stocks = $cart->cartProduct()->get();
    foreach ($stocks as $key => $value) {
        if($value->stock_id == $id){
            $data['cart'] = 1;
            $data['count'] += $value->count;
        }
    }
    return $data;
}
function getVariant($datas, $device_id){

    $variants = [];
    $in_cart_count = 0;
    foreach ($datas as $key => $value) {
        if($value->available) {
            $data = checkCartStock($value->id, $device_id);
            $variant = [
                'id' => $value->id,
                'in_cart' => $data['cart'],
                'cart_count' => $data['count'],
                'actual_price' => $value->actual_price,
                'price' => number_format($value->price, 2),
                'currency' => $value->currency,
                'variant' => $value->variant,
                'unit' => $value->unit,
                'available_count' => $value->available,
                'discount' => $value->actual_price > $value->price ? number_format((($value->actual_price - $value->price) / $value->actual_price) * 100, 2) : 0
            ];
            array_push($variants, $variant);
            $in_cart_count += $data['cart'];
        }
    }
    $result = ['variants' => $variants, 'total_count' => $in_cart_count];
    return $result;
}

function canShowShop($address_id, $shop){
    $show = AddressShop::where('address_id', $address_id)->where('shop_id', $shop->id)->where('alg', 0)->first();
    if($show && $show->kms <= $shop->radius){
        $time = $show->kms / 80 * 60 + 25;
        $data = ['int_dis' => round($show->kms, 1), 'int_time' => $time, 'distance' => number_format($show->kms, 1).' km', 'time' => number_format($time, 0).'-'.number_format($time + 10).' min'];
        return $data;
    }elseif(!$show){
        $address = Address::findOrFail($address_id);
        if(env('APP_IN_LIVE')){
            $data = get_distance($address->latitude, $address->longitude, $shop->latitude, $shop->longitude);
        }else{
            $data = get_distance($address->latitude, $address->longitude, $shop->latitude, $shop->longitude);
        }
        $add = AddressShop::updateOrCreate(['address_id' => $address->id, 'shop_id' => $shop->id], ['kms' => $data['int_dis'], 'alg' => $data['alg']]);
        return  $data;
    }else{
        $time = $show->kms / 80 * 60 + 25;
        $data = ['int_dis' => round($show->kms, 1), 'int_time' => $time, 'distance' => number_format($show->kms, 1).' km', 'time' => number_format($time, 0).'-'.number_format($time + 10).' min'];
    }
    return $data;
}

function scopeIsWithinMaxDistanceOld($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo) {

    //Calculate distance from latitude and longitude
    $theta = $longitudeFrom - $longitudeTo;
    $dist = sin(deg2rad($latitudeFrom)) * sin(deg2rad($latitudeTo)) +  cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;

    $distance = ($miles * 1.609344);
    $time = $distance / 80 * 60 + 25;
    $data = ['int_dis' => round($distance, 1), 'int_time' => $time, 'distance' => number_format($distance, 1).' km', 'time' => number_format($time, 0).'-'.number_format($time + 10).' min', 'alg' => 1];
    return $data;
}

 function get_distance($lat1, $lon1, $lat2, $lon2) {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        }
        else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $unit = "K";


            $miles = $dist * 60 * 1.1515;
            /*$meter = $miles / 1609.34;
            return $meter;*/
            $unit = strtoupper($unit);


                $km = $miles * 1.609344;
                $meter = $km /0.0010000;
                if($meter < 1000){
                    $dist = number_format($meter,0)." M";
                }else{
                    $dist = number_format($km,0)." KM";
                }
                $time = $km / 80 * 60 + 25;
                return ['int_dis' => $km ,'int_time' =>$time,'distance' => $dist,'time' => number_format($time, 0).'-'.number_format($time + 10).' min', 'alg' => 1];
          
        }
    }

function saveAddressDistance($address, $shop){
    $lat1 = $address->latitude;
    $long1 = $address->longitude;
    $lat2 = $shop->latitude;
    $long2 = $shop->longitude;
    // $res = file_get_contents('https://maps.googleapis.com/maps/api/directions/json?origin='.$lat1.','.$long1.'&destination='.$lat2.','.$long2.'&sensor=false&units=metric&key='.env('GEO_API'));
    $res = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?origins='.$lat1.','.$long1.'&destinations='.$lat2.','.$long2.'&sensor=false&units=metric&key='.env('GEO_API'));
    $res = json_decode($res);
    // dd($res);
    if($res->status !== 'OK'){
        // return ['status' => false, 'message' => 'Distance fetch failed!'];
        return saveAddressDistance($address, $shop);
    }
    $distance = (float)str_replace('km', '', $res->rows[0]->elements[0]->distance->text);
    $time = $distance / 80 * 60 + 25;
    $data = ['int_dis' => round($distance, 1), 'int_time' => $time, 'distance' => number_format($distance, 1).' km', 'time' => number_format($time, 0).'-'.number_format($time + 10).' min', 'alg' => 0];
    $add = AddressShop::updateOrCreate(['address_id' => $address->id, 'shop_id' => $shop->id], ['kms' => $data['int_dis'], 'alg' => $data['alg']]);

    return $data;
}

function scopeIsWithinMaxDistance($lat1, $long1, $lat2, $long2){
    // $res = file_get_contents('https://maps.googleapis.com/maps/api/directions/json?origin='.$lat1.','.$long1.'&destination='.$lat2.','.$long2.'&sensor=false&units=metric&key='.env('GEO_API'));
    $res = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?origins='.$lat1.','.$long1.'&destinations='.$lat2.','.$long2.'&sensor=false&units=metric&key='.env('GEO_API'));
    $res = json_decode($res);
    // dd($res);
    if($res->status !== 'OK'){
        // return ['status' => false, 'message' => 'Distance fetch failed!'];
        return scopeIsWithinMaxDistanceOld($lat1, $long1, $lat2, $long2);
    }
    $distance = (float)str_replace('km', '', $res->rows[0]->elements[0]->distance->text);
    $time = $distance / 80 * 60 + 25;
    $data = ['int_dis' => round($distance, 1), 'int_time' => $time, 'distance' => number_format($distance, 1).' km', 'time' => number_format($time, 0).'-'.number_format($time + 10).' min', 'alg' => 0];

    return $data;
}



function productsList($data){
    $products = [];
    foreach ($data as $key => $value) {
        $product = [
            'product_name' => $value['quantity'].' x '.$value['product_name'].' - '.$value['size'],
            'toppings' => $value['toppings']
        ];
        array_push($products, $product);
    }
    return $products;
}

function cartProducts($cart){
            $shop_details = [
                    'cart_id' => $cart->id,
                    'shop_id' => $cart->shop->id,
                    'shop_name' => $cart->shop->name,
                    'shop_street' => $cart->shop->street,
                    'shop_image' => $cart->shop->image,
                ];
                $products = [];
                $total_items = 0;
                foreach ($cart->cartProduct as $key => $value) {
                    $product = json_decode($value->product_details);
                    $stock = json_decode($value->stock_details);
                    $product = [
                        'id' => $stock->id,
                        'product_name' => $product->name,
                        'toppings' => json_decode($value->toppings),
                        'variety' => $product->variety,
                        'amount' => number_format($stock->price, 2),
                        'total_amount' => $value->amount,
                        'quantity' => $value->count .' ( '.$stock->size.' )' ?? $stock->variant.' '.$stock->unit.' )',
                        'size' => $stock->size ?? $stock->variant.' '.$stock->unit,
                        'unit' => $stock->unit
                ];
                    array_push($products, $product);
                    $total_items += 1;
                }
                $delivery_time = $cart->scheduled_at.' '.$cart->from.'-'.$cart->to;
                if($cart->type == 0){
                    $delivery_time = date('d-m-Y', strtotime(now()));
                }
                
                 $gst_charge = $cart->shop->gst == 1 ? calculateGSTCharge(str_replace(',', '', $cart->total_amount)) : 0;
            $gst = $gst_charge ? str_replace(',', '', $gst_charge) : 0;
            
                $tc = str_replace(',', '', $cart->total_amount);
            $dc = $cart->delivery_charge ? str_replace(',', '', $cart->delivery_charge) : 0;
            $ca = $cart->vendorcoupon_amount ? str_replace(',', '', $cart->vendorcoupon_amount) : 0;
            $grand_total = $cart->grand_total ? str_replace(',', '', $cart->grand_total) : 0;
                $data = ['shop_details' => $shop_details,  'products' => $products, 'delivery_time' => $delivery_time, 'total_items' => $total_items, 
                'coupon_discount' => $ca, 'delivery_charge' => $dc, 'tax' => $cart->tax,  'sub_total' => $tc,'grand_total'=> $grand_total,'total' => $grand_total + $gst + $cart->tax + $dc];

            return $data;
}

function cuisineShops($cuisines){
    try {

        foreach ($cuisines as $key => $cuisine) {
            $collection = $cuisine->shops;
            // $collection->merge($cuisine->shops);
        }

        return $collection;
    } catch (\Throwable $th) {
        return apiCatchResponse();
        //throw $th;
    }
}
function getOriginalClientIp(Request $request = null) : string
    {
        $request = $request ?? request();
        $xForwardedFor = $request->header('x-forwarded-for');
        if (empty($xForwardedFor)) {
            // Si está vacío, tome la IP del request.
            $ip = $request->ip();
        } else {
            // Si no, viene de API gateway y se transforma para usar.
            $ips = is_array($xForwardedFor) ? $xForwardedFor : explode(', ', $xForwardedFor);
            $ip = $ips[0];
        }
        return $ip;
    }

function quickGrab($products, $request){
    $quick = [];
    foreach ($products as $key => $item) {
        $cart_var = getVariant($item->stocks, $request->device_id);
        $single = [
            'product_id' => $item->id,
            "shop_id" => $item->shop->id,
            'product_name' => $item->name,
            'product_image' => $item->image,
            'description' => $item->description,
            'variety' => $item->variety,
            'variants' => $cart_var['variants']
        ];
        array_push($quick, $single);
    }
    return $quick;
}

// function sendDynamicOTP($mobile, $otp){

//         $accountSid = config('app.twilio')['TWILIO_ACCOUNT_SID'];
//         $authToken  = config('app.twilio')['TWILIO_AUTH_TOKEN'];
//         $client = new Client($accountSid, $authToken);
//         try
//         {
//             $client->messages->create(
//                 $mobile,
//            array(
//                  'from' => '+14055884019',
//                  'body' => 'Your VDeliverz Login OTP is '.$otp
//              )
//          );
//          return true;
//         }
//         catch (Exception $e)
//         {
//             return false;
//             echo "Error: " . $e->getMessage();
//         }
//     }
function sendDynamicOTP($mobile, $otp){
    $mobile = str_replace('+91', '', $mobile);
    $response = Http::get('http://msg.mtalkz.com/V2/http-api.php', [
        'apikey' => 'WJfC986InIcXxpGL',
        'senderid' => 'VDELVZ',
        'number' => $mobile,
        'message' => 'Thank You for signing up with VDeliverz. Your OTP for login to VDeliverz is '.$otp,
    ]);

    return true;
}




function shopsList($shops, $request){

    $filter_datas1 = [];
            $filter_datas2 = [];
            $c = 0;
            $shop_products = null;
            foreach ($shops as $k => $shop) {
                $data = canShowShop($request->address_id, $shop);
                if($data['int_dis'] <= $shop->radius){
                if($c < 2){
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
                    "is_opened" => $shop->is_opened
                    ];
                    array_push($filter_datas1, $single);
                    $shop_products = $shop;
                }else{
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
                    "is_opened" => $shop->is_opened
                    ];
                    array_push($filter_datas2, $single);
                }
                $c += 1;
                }
            }
            $merged = array_merge($filter_datas1, $filter_datas2);
            return ['merged' => $merged, 'data1' => $filter_datas1, 'data2' => $filter_datas2, 'products' => isset($shop_products) ? $shop_products->products()->where('category_id', $request->shop_category_id)->get() : null];
}

function moveCartToUser($device_id, $user){
    try {
        $old_cart = \App\Cart::where('user_id', $user->id)->where('checkout', 0)->first();
        $new_cart = \App\Cart::where('device_id', $device_id)->where('checkout', 0)->first();
        $booking = \App\Booking::where('device_id', $device_id)->where('status', 0)->where('provider_sub_service_id', '!=', null)->first();
        if($new_cart){
            $del = isset($old_cart) ? $old_cart->delete() : null;
            $new_cart->user_id = $user->id;
            $new_cart->device_id = null;
            $new_cart->save();
        }
        if($booking){
            $booking->update(['user_id' => $user->id]);
        }
        if($car_booking){
            $car_booking->update(['user_id' => $user->id]);
        }
        return true;
    } catch (\Throwable $th) {
        return apiCatchResponse();
        dd($th);
    }
}

function resetToppings($toppings){
    try {
        foreach ($toppings as $top){
            $topping = \App\Topping::where('id', $top)->first();
            if ($topping){
                $topping->available += 1;
                $topping->save();
            }
        }
    } catch (\Throwable $th) {
        return apiCatchResponse();
        dd($th);
    }
}

function calculateDeliveryCharge($distance){

        $charge = Charge::first();
        $amount = 0;
        if($distance <= $charge->basic_km){
            $amount += $charge->basic_charge;
        }
        if($distance > $charge->basic_km){
            $amount += (($distance - $charge->basic_km) * $charge->extra_charge) + $charge->basic_charge;
        }
        return $amount;
    }
    
    
    
     //  $iv = "@@@@&&&&####$$$$";
    function generateSignature($params, $key) {
     //   dump($key);
		if(!is_array($params) && !is_string($params)){
			throw new Exception("string or array expected, ".gettype($params)." given");			
		}
	//	dump("sasasa");
		if(is_array($params)){
			$params = getStringByParams($params);			
		}
	//	dd($params);
		return generateSignatureByString($params, $key);
	}
	
	function getStringByParams($params) {
		ksort($params);		
		
		$params = array_map(function ($value){
			return ($value !== null && strtolower($value) !== "null") ? $value : "";
	  	}, $params);
		return implode("|", $params);
	}



	function payencrypt($input, $key) {
		$key = html_entity_decode($key);
         $iv = "@@@@&&&&####$$$$";
		if(function_exists('openssl_encrypt')){
			$data = openssl_encrypt ( $input , "AES-128-CBC" , $key, 0, $iv);
		} else {
			$size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, 'cbc');
			$input = pkcs5Pad($input, $size);
			$td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', 'cbc', '');
			mcrypt_generic_init($td, $key, $iv);
			$data = mcrypt_generic($td, $input);
			mcrypt_generic_deinit($td);
			mcrypt_module_close($td);
			$data = base64_encode($data);
		}
		return $data;
	}

	 function paydecrypt($encrypted, $key) {
		$key = html_entity_decode($key);
		 $iv = "@@@@&&&&####$$$$";
		if(function_exists('openssl_decrypt')){
			$data = openssl_decrypt ( $encrypted , "AES-128-CBC" , $key, 0, $iv);
		} else {
			$encrypted = base64_decode($encrypted);
			$td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', 'cbc', '');
			mcrypt_generic_init($td, $key, $iv);
			$data = mdecrypt_generic($td, $encrypted);
			mcrypt_generic_deinit($td);
			mcrypt_module_close($td);
			$data = pkcs5Unpad($data);
			$data = rtrim($data);
		}
		return $data;
	}



	 function verifySignature($params, $key, $checksum){
		if(!is_array($params) && !is_string($params)){
			throw new Exception("string or array expected, ".gettype($params)." given");
		}
		if(isset($params['CHECKSUMHASH'])){
			unset($params['CHECKSUMHASH']);
		}
		if(is_array($params)){
			$params = getStringByParams($params);
		}		
		return verifySignatureByString($params, $key, $checksum);
	}

	 function generateSignatureByString($params, $key){
		$salt = generateRandomString(4);
		return calculateChecksum($params, $key, $salt);
	}

	 function verifySignatureByString($params, $key, $checksum){
		$paytm_hash = paydecrypt($checksum, $key);
		$salt = substr($paytm_hash, -4);
		return $paytm_hash == calculateHash($params, $salt) ? true : false;
	}

	 function generateRandomString($length) {
		$random = "";
		srand((double) microtime() * 1000000);

		$data = "9876543210ZYXWVUTSRQPONMLKJIHGFEDCBAabcdefghijklmnopqrstuvwxyz!@#$&_";	

		for ($i = 0; $i < $length; $i++) {
			$random .= substr($data, (rand() % (strlen($data))), 1);
		}

		return $random;
	}



	 function calculateHash($params, $salt){
		$finalString = $params . "|" . $salt;
		$hash = hash("sha256", $finalString);
		return $hash . $salt;
	}

	 function calculateChecksum($params, $key, $salt){
		$hashString = calculateHash($params, $salt);
	//	dd($hashString);
		return payencrypt($hashString, $key);
	}

	 function pkcs5Pad($text, $blocksize) {
		$pad = $blocksize - (strlen($text) % $blocksize);
		return $text . str_repeat(chr($pad), $pad);
	}

	 function pkcs5Unpad($text) {
		$pad = ord($text[strlen($text) - 1]);
		if ($pad > strlen($text))
			return false;
		return substr($text, 0, -1 * $pad);
	}   
    
    function calculateGSTCharge($total_amount){

        $charge = Charge::first();
        $amount = 0;

        $amount += (($total_amount) * $charge->gst_charge/100);
       
        return $amount;
    }
    
       function get_shopopenclosedtl($shop){
        
      $Shop_openclose_dtl='';$shopOpened ='';$color = '';
        $shop_dtl = \App\Shop::where('id',$shop->id)->select(DB::raw("(GROUP_CONCAT(weekdays SEPARATOR ',')) as `weekdays`"))->get();
        $arr = explode(',', $shop_dtl[0]->weekdays); 
        $Today_dayname = \Carbon\Carbon::now()->format('l');
        if(in_array($Today_dayname, $arr) == false)
        {
            $coupons = \App\VendorCoupon::where('shop_id',$shop->id)->whereDate('expired_on', '>=', now())->where('active',1)->get();
        
            $todate = \Carbon\Carbon::parse($shop->closes_at)->toDatetimeString();
            $fromdate = \Carbon\Carbon::parse($shop->opens_at)->toDatetimeString();
            
            $to = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $todate); 
            $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $fromdate);
            $now = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',\Carbon\Carbon::now()); 
            
            $Shop_openclose_dtl ='Today is a weekoff'; $color='false';
        }
        else
        {
        $coupons = \App\VendorCoupon::where('shop_id',$shop->id)->whereDate('expired_on', '>=', now())->where('active',1)->get();
        
        $todate = \Carbon\Carbon::parse($shop->closes_at)->toDatetimeString();
        $fromdate = \Carbon\Carbon::parse($shop->opens_at)->toDatetimeString();
        
        $to = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $todate); 
        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $fromdate);
        $now = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',\Carbon\Carbon::now());
        
        // $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', '2022-05-27 10:00:00');

        $openingTimeDiff =  $now->diffInMinutes($from);
        $closingTimeDiff = $to->diffInMinutes($now);
       
       
        if($shop->opened ==0)
         { $time = date('H:i A', strtotime($from));
           $Shop_openclose_dtl = 'We are temporarily not accepting online orders at this time, please try again after sometime'; $color = 'false';}
        else{   
        if($now->between($from, $to))
         { $shopOpened = true; $Shop_openclose_dtl = 'We are serving now.'; $color = 'true'; }
         else if($from>$now && $openingTimeDiff < 30 && $openingTimeDiff > 0) 
         {  $Shop_openclose_dtl = 'Shop opens in '. $openingTimeDiff .' minutes'; $color = 'true'; }
         
         if($now->between($from, $to) == false && $Shop_openclose_dtl == '')
         { $time = date('g:i a', strtotime($from));
           $Shop_openclose_dtl = 'We are closed for the day. We reopen at '. $time ; $color = 'false';}
        
        if($to>$now == true && $closingTimeDiff < 30 && $closingTimeDiff > 0)
         { $shopOpened = true; $Shop_openclose_dtl = 'Shop closes in '. $closingTimeDiff .' minutes'; $color = 'false'; }
        }
      }
    $result = ['shopOpened' => $shopOpened, 'from' => $from, 'to' => $to, 'coupons' => $coupons,'Shop_openclose_dtl' => $Shop_openclose_dtl, 'color' => $color];
    return $result;
}
    
    
 