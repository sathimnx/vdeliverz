<?php

namespace App\Http\Controllers\Api\v6;

use App\Order;
use App\Stock;
use App\VendorCoupon;
use Validator, DB, Auth;
use Illuminate\Http\Request;
use App\Events\PostPublished;
use App\Http\Controllers\Controller;
use App\Http\Resources\StockResource;

class BaseController extends Controller
{
    //public function dashboard(){
        //try {
            //$categories = categoriesList();
            //$cat1 = $categories->splice(4);
            //$notify = auth('api')->user() ? auth('api')->user()->notify_count : 0;
            //$wish = auth('api')->user() ? auth('api')->user()->wishlist_count : 0;
            //$banners = appBanners();
            //$contact = \App\Contact::first(['mobile', 'wp as whatsapp', 'email']);
            //$shop_banners = \App\ShopBanner::where('active', 1)->inRandomOrder()->limit(8)->get(['id as shop_banner_id', 'image']);
            //return response()->json(['status' => true,
            //'message' => 'Dashboard Data', 'notification_count' => $notify, 'wishlist_count' => $wish,
            //'category1' => $categories, 'category2' => $cat1, 'terms_and_conditions' => 'http://vdeliverz.in/terms-and-conditions/',
             //'privacy_policy' => 'http://vdeliverz.in/privacy-policy/', 'banners' => $banners, 'shop_banners' => $shop_banners, 'conatct' => $contact
            //]);
        //} catch (\Throwable $th) {
           // return apiCatchResponse();
          //  dd($th);
        //}
    //}
    public function dashboard(Request $request){
        try {
            $categories = categoriesList();
            $cat1 = $categories->splice(4);
            $notify = auth('api')->user() ? auth('api')->user()->notify_count : 0;
            $wish = auth('api')->user() ? auth('api')->user()->wishlist_count : 0;
            $banners = appBanners();
            $contact = \App\Contact::first(['mobile', 'wp as whatsapp', 'email']);
            $shop_banners = \App\ShopBanner::where('active', 1)->inRandomOrder()->limit(8)->get(['id as shop_banner_id', 'image']);
            if($request->fcm_token != null || $request->fcm_token != '')
            {
                $user = \App\User::where('id', auth('api')->user())->first();
                $user->update(['fcm' => $request->fcm_token]);
            }
            
            return response()->json(['status' => true,
            'message' => 'Dashboard Data', 'notification_count' => $notify, 'wishlist_count' => $wish,
            'category1' => $categories, 'category2' => $cat1, 'terms_and_conditions' => 'http://vdeliverz.in/terms-and-conditions/',
             'privacy_policy' => 'http://vdeliverz.in/privacy-policy/', 'banners' => $banners, 'shop_banners' => $shop_banners, 'conatct' => $contact
            ]);
        } catch (\Throwable $th) {
           // return apiCatchResponse();
            dd($th);
        }
    }
    
    
    // 11.335704351856997, 79.6366725051649
    // 11.397286803293433, 79.69313748898985
    //Get Shops List for a category
    public function shops(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                // 'latitude' => 'required',
                // 'longitude' => 'required',
                'device_id' => 'required',
                'address_id' => 'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            $quick = [];
            $day = now()->format('l');
            $time = now()->format('H:i:s');
            $cat_id = $request->id;
            $collection = \App\Shop::where('active', 1)->whereHas('products', function($q) use($cat_id){
                $q->where('category_id', $cat_id)->where('active', 1);
            })->orderBy('prior')->get();

            $c = 0;
            $products = [];
            $shops = [];
            foreach ($collection as $k => $shop) {
                $Shop_opencloseDetails = get_shopopenclosedtl($shop);
                 if(\App\VendorCoupon::whereDate('expired_on', '>=', now())->where('shop_id', 0)->get()->count() > 0)
                {
                     $coupons = \App\VendorCoupon::whereDate('expired_on', '>=', now())->where('shop_id', 0)->get()->count();
                }
                else
                {
                     $coupons = \App\VendorCoupon::where('shop_id', $shop->id)->whereDate('expired_on', '>=', now())->get()->count();
                }
               $data = canShowShop($request->address_id, $shop);
                if($data['int_dis'] <= $shop->radius && $shop->active == 1){
                    $shops[] = [
                        'shop_id' => $shop->id,
                        'shop_name' => $shop->name,
                        'shop_image' => $shop->image,
                        'shop_area' => $shop->area,
                        'shop_address' => $shop->street.', '.$shop->area.', '.$shop->city,
                        'price' => number_format($shop->sprice, 2).' '.$shop->currency,
                       'distance' => $data['distance'],
                        'time' => $data['time'],
                        'rating' => $shop->rating_avg,
                        'rating_count' => $shop->rating_count,
                        "is_opened" => $shop->is_opened,
                        "coupon_available"=> $coupons > 0 ?true:false,
                        
                        "shop_isopened" => $Shop_opencloseDetails['shopOpened'] =='' ? false : $Shop_opencloseDetails['shopOpened'],
                        "opening_time" => $Shop_opencloseDetails['from'],
                        "closing_time" => $Shop_opencloseDetails['to'],
                        "coupons" => $Shop_opencloseDetails['coupons'],
                        "shop_openclose_dtl"=> $Shop_opencloseDetails['Shop_openclose_dtl'],
                        "iscoloured_blue" =>$Shop_opencloseDetails['color']
                    ];
                    $products = array_merge($products, $shop->products()->get()->toArray());
                }
            }
            shuffle($products);
            $products = collect($products)->take(11);
            foreach ($products as $key => $item) {
                $stocks = Stock::where('product_id', $item['id'])->where('available', '>', 0)->get();
                if($stocks->isNotEmpty()){
                    $cart_var = getVariant($stocks, $request->device_id);
                    $quick[] = [
                        'product_id' => $item['id'],
                        'product_name' => $item['name'],
                        'product_image' => $item['image'],
                        'description' => $item['description'],
                        'variety' => $item['variety'],
                        'variants' => $cart_var['variants']
                    ];
                }
            }


            return response()->json(['status' => true, 'message' => 'Shops List', 'shop_category_id' => $request->id,
             'shops' => collect($shops)->take(2), 'shops1' => collect($shops)->splice(2), 'products' => $quick ?? []]);
        } catch (\Throwable $th) {
            return ['status' => false, 'message' => "Can't able to fetch distance!"];
            dd($th);
        }
    }

    public function vendor_coupondtl(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'categoryid' => 'required',
            ]);
              if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }  
           $coupons=[];
            if(\App\VendorCoupon::whereDate('expired_on', '>=', now())->where('shop_id', 0)->get()->count() > 0)
            {
                 $couponsdtl = \App\VendorCoupon::whereDate('expired_on', '>=', now())->where('category_id', $request->categoryid)
                 ->whereIn('shop_id', array(0,$request->id))->get();
                 
                  foreach ($couponsdtl as $key => $coupon) {
                           $coupon_dtl = [
                            'coupon_code' => $coupon->coupon_code,
                            'expired_on' => $coupon->expired_on,
                            'max_order_amount' => $coupon->max_order_amount,
                            'coupon_percentage' => $coupon->coupon_percentage,
                            'symbol' => $coupon->symbol,
                            'coupon_description' => $coupon->coupon_description,
                            'Discount_use_amt' => $coupon->Discount_use_amt,
                            'min_order_amt' => $coupon->min_order_amt,
                            'shop_name' => $coupon->shop_name,
                            'sub_category_name' => $coupon->sub_category_name,
                            'product_name' => $coupon->product_name
                     ];
                       array_push($coupons, $coupon_dtl);
                  }
            }
            else
            {
                if(\App\VendorCoupon::whereDate('expired_on', '>=', now())->whereIn('shop_id', array($request->id))->where('category_id',$request->categoryid)->get()->count() > 0)
                {
                   $couponsdtl = \App\VendorCoupon::whereDate('expired_on', '>=', now())->whereIn('shop_id', array($request->id))
                    ->where('category_id',$request->categoryid)->get();
                
                     foreach ($couponsdtl as $key => $coupon) {
                           $coupon_dtl = [
                            'coupon_code' => $coupon->coupon_code,
                            'expired_on' => $coupon->expired_on,
                            'max_order_amount' => $coupon->max_order_amount,
                            'coupon_percentage' => $coupon->coupon_percentage,
                            'symbol' => $coupon->symbol,
                            'coupon_description' => $coupon->coupon_description,
                            'Discount_use_amt' => $coupon->Discount_use_amt,
                            'min_order_amt' => $coupon->min_order_amt,
                            'shop_name' => $coupon->shop_name,
                            'sub_category_name' => $coupon->sub_category_name,
                            'product_name' => $coupon->product_name
                     ];
                       array_push($coupons, $coupon_dtl);
                     
                     }
                 }
                
            }
             
              return response()->json(['status' => true, 'coupon'=>$coupons]);
        } catch (\Throwable $th) {
            return ['status' => false, 'message' => "Coupons not available"];
            dd($th);
        }
    }
    
     //Get products List for a category
     public function products(Request $request){
        try {
       $validator = Validator::make($request->all(), [
                'shop_id' => 'required',
                'device_id' => 'required',
                'shop_category_id' => 'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            $shop = \App\Shop::where('id', $request->shop_id)->first();
            if(!$shop){
                return response()->json(['status' => false, 'message' => 'No shops Found!']);
            }
            
            if(\App\VendorCoupon::whereDate('expired_on', '>=', now())->where('shop_id', 0)->get()->count() > 0)
            {
                 $coupons = \App\VendorCoupon::whereDate('expired_on', '>=', now())->where('shop_id', 0)->get()->count();
            }
            else
            {
                 $coupons = \App\VendorCoupon::where('shop_id', $shop->id)->whereDate('expired_on', '>=', now())->get()->count();
            }
            
           $data = canShowShop($request->address_id, $shop);
           $Shop_opencloseDetails = get_shopopenclosedtl($shop); 
            $shop_details = [
                'shop_id' => $shop->id,
                'shop_name' => $shop->name,
                'shop_image' => $shop->image,
                'banner' => $shop->banner_image,
                'shop_area' => $shop->area,
                'shop_address' => $shop->street.', '.$shop->area.', '.$shop->city,
                'distance' => $data['distance'],
                'time' => $data['time'],
                'price' => $shop->price.' '.$shop->currency,
                'rating' => $shop->rating_avg,
                'rating_count' => $shop->rating_count,
                'is_wishlist' => $shop->wishlist(),
                'is_cart' => $shop->cart($request->device_id),
                'free_delivery' => $shop->delivery_charge == 0 ? true : false,
                "is_opened" => $shop->is_opened,
                
                "coupon_available" => $coupons > 0 ? true:false,
                "shop_isopened" => $Shop_opencloseDetails['shopOpened'] =='' ? false : $Shop_opencloseDetails['shopOpened'],
                "opening_time" => $Shop_opencloseDetails['from'],
                "closing_time" => $Shop_opencloseDetails['to'],
                //"coupons" => $Shop_opencloseDetails['coupons'],
                "shop_openclose_dtl"=> $Shop_opencloseDetails['Shop_openclose_dtl'],
                "iscoloured_blue" =>$Shop_opencloseDetails['color']
                
            ];
           // dd($shop_details);
            $items = [];
            $subCategories = [];
            //->where('rec',1)
             $products = \App\Product::where('shop_id', $request->shop_id)->where('active', 1)->where('category_id', $request->shop_category_id)
            ->with('stocks', 'shop', 'subCategory')->orderBy('updated_at', 'desc')->get();
            $categories = $products->unique('sub_category_id'); 
            foreach ($products as $key => $product) {
                $NextAvailableTime ='';
                $cart_var = getVariant($product->stocks, $request->device_id);
                $product_timings = DB::table('product_opencloseTime')->where('product_id', $product->id)->get();
                $now = \Carbon\Carbon::now();
                
                $isavailable = 1;
                if(isset($product_timings) && isset($product_timings[0]))
                {
                    foreach ($product_timings as $key => $product_timing) {
                        
                        $start = \Carbon\Carbon::parse($product_timing->open_time)->toDatetimeString(); 
                        $end = \Carbon\Carbon::parse($product_timing->close_time)->toDatetimeString();
                        
                        //$NextAvailableTime = \Carbon\Carbon::parse($start)->gt(\Carbon\Carbon::now());
                        //dd($NextAvailableTime);
                        if ($now->between($start, $end)) {
                            $isavailable = 1;
                        }
                        elseif(\Carbon\Carbon::parse($start)->gt(\Carbon\Carbon::now()))
                        {
                            //dd($product->id);
                            
                             if($NextAvailableTime == '') { $NextAvailableTime = $start; $isavailable = 0; }
                        }
                        elseif(\Carbon\Carbon::parse($start)->gt(\Carbon\Carbon::now()) == false)
                        {
                            $isavailable = 0; 
                        }
                    }
                   $time =  $product_timing->open_time . ' to ' .$product_timing->close_time;
                }
                      $item = [
                                'product_id' => $product->id,
                                'product_name' => $product->name,
                                'product_image' => $product->image,
                                'description' => $product->description,
                                'can_customize' => $product->can_customize,
                                'variety' => $product->variety,
                                'avilable_timings' =>  isset($time) ? $time : '' ,
                                'isavailable' => $isavailable,
                                'next_available_time' => $isavailable == 0 ? ($NextAvailableTime == '' ? '' : 'Next available time '. 
                                date('g:i a', strtotime($NextAvailableTime)) .':'. date('g:i a', strtotime($end)) ) : '',
                                
                                'in_cart_total' => collect($cart_var['variants'])->sum('cart_count'),
                                'variants' => isset($cart_var['variants'][0]) ? [$cart_var['variants'][0]] : ''
                            ];
                            if(count($cart_var['variants']) > 0){
                                array_push($items, $item);
                            } 
            }
             $subCategory =  \App\ShopSubCategory::with('subCategory')->where('shop_id', $request->shop_id)->where('active', 1)->orderBy('order','asc')->get();
        
            foreach ($subCategory as $key => $category) {
                $subCategor =  \App\SubCategory::where('id', $category->sub_category_id)->get('name');
                $subCategor =  \App\SubCategory::where('id', $category->sub_category_id)->get('name');
                $products_count = \App\Product::where('shop_id', $request->shop_id)->where('active', 1)->where('sub_category_id', $category->sub_category_id)->get()->count();
               
                if($products_count > 0){
                    $cat = [
                        'category_id' => $category->sub_category_id,
                        'category_name' => $subCategor[0]->name,
                        'category_count' => \App\Product::where('sub_category_id', $category->sub_category_id)->where('shop_id', $request->shop_id)->count()
                        ];
                    array_push($subCategories, $cat);
                }
            }
              //$subCategories =array_reverse($subCategories);
           
            return response()->json(['status' => true, 'message' => 'Products List', 'recommended_count' => $products->count(), 'shop_details' => $shop_details,
             'categories' => $subCategories, 'shop_products' => collect($items)->take(15)]);
        } catch (\Throwable $th) {
          // return apiCatchResponse();
            dd($th);
        }
    }

    public function categories(){
        try {
            $other_services = \App\Service::where('active', 1)->orderBy('order', 'asc')->get(['id', 'name', 'image']);
            return response()->json(['status' => true,
            'message' => 'Categories Data',
            'categories' => categoriesList(),
            'other_categories' => $other_services
            ]);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            //throw $th;
        }
    }
    public function singleShop(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'shop_id' => 'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            $shop = \App\Shop::where('id', $request->shop_id)->first();
            $reviews = [];
            foreach ($shop->reviews()->latest()->take(10)->get() as $key => $value) {
                $review = [
                    'username' => $value->user->name,
                    'rating' => $value->rating,
                    'time' => $value->timeago,
                ];
                array_push($reviews, $review);
            }
            $shop = [
                'shop_id' => $shop->id,
                'shop_name' => $shop->name,
                'banner_image' => $shop->banner_image,
                'shop_area' => $shop->area,
                'rating' => $shop->rating_avg,
                'rating_count' => $shop->rating_count,
                'is_wishlist' => $shop->wishlist(),
            ];
            return response()->json(['status' => true, 'message' => 'Shop Details', 'shop' => $shop, 'recent_reviews' => $reviews ]);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            dd($th);
        }
    }

    public function categoryProducts(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'category_id' => 'required',
                'device_id' => 'required',
                'shop_id' => 'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            $items = []; $NextAvailableTime ='';
            $products = \App\Product::where('sub_category_id', $request->category_id)->where('shop_id', $request->shop_id)->where('active', 1)->with('stocks', 'shop', 'subCategory')->get();
            
            if($products->isEmpty()){
                return response()->json(['status' => false, 'message' => 'No Products Found!']);
            }
            $categories = $products->unique('sub_category_id');
            foreach ($products as $key => $product) {
                $cart_var = getVariant($product->stocks, $request->device_id);
                $product_timings = DB::table('product_opencloseTime')->where('product_id', $product->id)->get();
                $now = \Carbon\Carbon::now();
                
                $isavailable = 1;
                if(isset($product_timings) && isset($product_timings[0]))
                {
                    foreach ($product_timings as $key => $product_timing) {
                        
                        $start = \Carbon\Carbon::parse($product_timing->open_time)->toDatetimeString(); 
                        $end = \Carbon\Carbon::parse($product_timing->close_time)->toDatetimeString();
                        
                        if ($now->between($start, $end)) {
                            $isavailable = 1;
                        }
                        elseif(\Carbon\Carbon::parse($start)->gt(\Carbon\Carbon::now()))
                        {
                             if($NextAvailableTime == '') { $NextAvailableTime = $start; $isavailable = 0; }
                        }
                        elseif(\Carbon\Carbon::parse($start)->gt(\Carbon\Carbon::now()) == false)
                        {
                            $isavailable = 0; 
                        }
                    }
                }
                
                $item = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_image' => $product->image,
                    'description' => $product->description,
                    'variety' => $product->variety,
                    'can_customize' => $product->can_customize,
                    'isavailable' => $isavailable,
                    'next_available_time' => $isavailable == 0 ? ($NextAvailableTime == '' ? '' : 'Next available time '. 
                      date('g:i a', strtotime($NextAvailableTime)) .':'. date('g:i a', strtotime($end)) ) : '',
                    'in_cart_total' => collect($cart_var['variants'])->sum('cart_count'),
                    'variants' => isset($cart_var['variants'][0]) ? [$cart_var['variants'][0]] : ''
            ];
                if(count($cart_var['variants']) > 0){
                    array_push($items, $item);
                }
            }
            $shop = \App\Shop::find($request->shop_id);
            return response()->json(['status' => true, 'message' => 'Products List', 'is_cart' => $shop->cart($request->device_id), 'shop_products' => $items]);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            dd($th);
        }
    }

    public function productCustomize(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
//            $users = \App\User::role(['admin', 'vendor'])->get();
//            return response($users);
            $product = \App\Product::where('id', $request->product_id)->where('active', 1)->first();
//            event(new PostPublished($product));
            $toppings = $product->toppings()->where('available', '>', 0)->whereHas('title', function ($query){
                $query->where('active', 1);
            })->get(['id as topping_id', 'name', 'price', 'variety']);
            $stocks = $product->stocks;
            $quantity = [];
            foreach ($stocks as $stock){
                $quantity[] = ['id' => $stock->id, 'size' => $stock->size ? $stock->size : $stock->variant.' '.$stock->unit, 'variety' => $product->variety, 'price' => number_format($stock->price, 2)];
            }
            $p_name = ['product_name' => $product->name, 'variety' => $product->variety];
            return response(['status' => true, 'message' => 'Toppings List', 'product' => $p_name, 'quantity' => $quantity, 'toppings' => $toppings]);
        } catch (\Throwable $th) {
           return apiCatchResponse();
            dd($th);
        }
    }

    public function changeOrders(){
        $orders = \App\Order::all();
        foreach($orders as $order){
            $order->update(["search" => $order->prefix.$order->id]);
        }
        return "success";
    }

    public function pagination(){
        $products = \App\Product::paginate(5);
        return response(['status' => true, 'message' => 'Products pagination', 'products' => $products]);
    }

    public function sendfcm(){
        $data = [
            'to' => 'ckjIIgZAQSCnKp4ZY9mi-G:APA91bESknAoe_7qvZyKWlrhr_56rdrevNS6icEx8NLeswAOOvtiMYV7goitV0LQGS_2I9ANo0nTOd8ubrufrq6DLEWRUNnSJSSBjC3WQJoEvja3MU3M3ff8CUZWImVx0NQ3LELhPAZW',
            'data' => [
                "data" => [
                    "title" => "From Vdeliverz",
                    "message" => "054645656565 is received now",
                    "image" => "sample.png",
                    "type" => "1"
                ]
            ]
        ];
        sendPushNotification($data, 'AAAA0DJLyxg:APA91bGV1_HGNLhVp5dcT_lJ8LXpmYoubhhoHWS3e9lQ13HpBA8cjsFtI-dtRgNV1gJJxniL7rOUpklJwy6--surLkrxw2X4-KE87wzG0HF8SBD6PRM-o8mge2BgRzy37vRINwXgz3RN');
        
        return "Push notification Send.";
    }
    public function orderKms(){
        $orders = Order::where('order_status', 3)->where('kms', null)->get();
        foreach ($orders as $key => $order) {
            if(!$order->kms){
                $address = json_decode($order->address);
                $distance = canShowShop($order->shop->latitude, $order->shop->longitude, $address->latitude, $address->longitude);
                $order->update(['kms' => $distance['int_dis']]);
            }
        }
        return 'Updated';
    }
    
    public function editchecks(Request $request)
    {
        try {
            $word = preg_replace('/\s+/', "", strtolower($request->coupon_code));;
            $coupon_date = date('Y-m-d', strtotime($request->coupon_date));
            $coupon_time = date('H:i:s', strtotime($request->coupon_time == '' ||null ? now() :$request->coupon_time));
            $coupon_end = $coupon_date.' '. $coupon_time;
        
            $coupon = new VendorCoupon;
            $coupon->coupon_code = $request->coupon_code;
            $coupon->coupon_description = $request->coupon_description;
            $coupon->coupon_percentage = $request->coupon_percentage;
            $coupon->max_order_amount = $request->max_order_amount;
            $coupon->min_order_amt = $request->min_order_amt;
            $coupon->Discount_use_amt =$request->Discount_use_amt;
            $coupon->expired_on = $coupon_end;
           
            
            $product_getCategory = \App\Product::where('shop_id',$request->shop_id)->first();
            
            $coupon->category_id = $product_getCategory->category_id;
            
             
             //dd($request->couponShop,$request->product_category[0],$request->products);
             $ShopName = 
            \App\Shop::where('id',$request->shop_id)->select(DB::raw("(GROUP_CONCAT(id SEPARATOR ',')) as `shopId`"),DB::raw("(GROUP_CONCAT(name SEPARATOR ',')) as `shopname`"))->get();
           
            $subcategory = explode(',',$request->product_category);  
            $products = explode(',',$request->product_dtl);  

            $CategoryName = $request->product_category[0] == '0' ? '0' :
            \App\SubCategory::whereIn('id',$subcategory)
            ->select(DB::raw("(GROUP_CONCAT(id SEPARATOR ',')) as `categoryId`"),DB::raw("(GROUP_CONCAT(name SEPARATOR ',')) as `categoryname`"))->get();
            
            $ProductName = $request->product_dtl =='0' || $request->product_dtl == null ? '0' :
            \App\Product::whereIn('id',$products)->select(DB::raw("GROUP_CONCAT(id SEPARATOR ',') as productId"),DB::raw("GROUP_CONCAT(name SEPARATOR ',') as productname"))->get();
         
         //dd($request->product_category,$ShopName[0],$CategoryName[0],$ProductName[0]);
            $coupon->shop_id = $request->shop_id;
            $coupon->sub_category_id = $CategoryName[0] =='0' ? 0 : $CategoryName[0]->categoryId;
            $coupon->product_id = $ProductName[0] =='0' ? 0 : $ProductName[0]->productId;
            $coupon->shop_name = $ShopName[0]->shopname;
            $coupon->sub_category_name = $CategoryName[0] =='0' ? 'All Sub Category' :  $CategoryName[0]->categoryname;
            $coupon->product_name = $ProductName[0] =='0' ? 'All Products' :  $ProductName[0]->productname;
            dd($coupon);
        } catch (\Throwable $th) {
           // return ['status' => false, 'message' => "Can't able to fetch distance!"];
            dd($th);
        }
    }
    
    public function dTd_deliveryBooking(Request $request)
    {
        try
        { 
            $validator = Validator::make($request->all(), [
                'pickup_name'=>'required',
                'pickup_date'=>'required',
                'pickup_time'=>'required',
                'pickup_mobile'=>'required',
                'pickup_address'=>'required',
                'pickup_address_area'=>'required',
                'pickup_address_landmark'=>'required',
                'pickup_address_addresstype'=>'required',
                'pickup_address_latitude'=>'required',
                'pickup_address_longitude'=>'required',
                'pickup_item_name'=>'required',
                'pickup_item_dtl'=>'required',
                'pickup_item_quantity'=>'required',
               // 'approximate_weight'=>'required',
               
                'pickup_item_image'=>'required',
                'transportation_type'=>'required',
                // 'four_wheelervehicle_type'=>'required',
              
                'droupup_name'=>'required',
                'droupup_mobile'=>'required',
                'droupup_address'=>'required',
                'droupup_address_area'=>'required',
                'droupup_address_landmark'=>'required',
                'droupup_address_addresstype'=>'required',
                'droupup_address_latitude'=>'required',
                'droupup_address_longitude'=>'required',
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            
            $pickup_address=array('name'=>$request->pickup_name,'address'=>$request->pickup_address,'area'=>$request->pickup_address_area,'landmark'=>$request->pickup_address_landmark,'address_type'=>$request->pickup_address_addresstype,
            'latitude'=>$request->pickup_address_latitude,'longitude'=>$request->pickup_address_longitude,'phone_number'=>$request->pickup_mobile);
            
            $Pickup_addressId = DB::table('dTd_pickup_address_dtls')->insertGetId($pickup_address);
            
             if($request->hasFile('pickup_item_image')){
              if($request->file('pickup_item_image')->isValid())
                {

                    $extension = $request->pickup_item_image->extension();
                    $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->pickup_item_name)).time()."cat." .$extension;
                    $request->pickup_item_image->move(config('constants.category_icon_image'), $file_path);
                }
            }
            
            $pickup_itemDetail=array('item_name'=>$request->pickup_item_name,'item_detail'=>$request->pickup_item_dtl,'quantity'=>$request->pickup_item_quantity,
            //'approximate_weight'=>$request->approximate_weight,
            'image'=>$file_path);
            
            $Pickup_itemId = DB::table('dTd_pickup_dtls')->insertGetId($pickup_itemDetail);
            
            $dropup_address=array('name'=>$request->droupup_name,'address'=>$request->droupup_address,'area'=>$request->droupup_address_area,'landmark'=>$request->droupup_address_landmark,'address_type'=>$request->droupup_address_addresstype,
            'latitude'=>$request->droupup_address_latitude,'longitude'=>$request->droupup_address_longitude,'phone_number'=>$request->droupup_mobile);
            
            $Dropup_addressId = DB::table('dTd_drop_address_dtls')->insertGetId($dropup_address);
            
            $DoorToDoorDelivery=array('user_id'=>auth('api')->user()->id,'pickup_date'=>$request->pickup_date,'pickup_time'=>$request->pickup_time,'pickup_mobile'=>$request->pickup_mobile,'pickup_addressId'=>$Pickup_addressId,
            'pickup_itemId'=>$Pickup_itemId,'transportation_type'=>$request->transportation_type,
            'droupup_mobile'=>$request->droupup_mobile,'droupup_addressId'=>$Dropup_addressId);
            
            $order_id = DB::table('door_to_doorDelivery')->insertGetId($DoorToDoorDelivery);
            
            $dTd_delivery_orders = DB::table('door_to_doorDelivery')->where('order_id',$order_id)->first();
            
            
            $items = [];
               if(isset($dTd_delivery_orders) ) { 
                 $item = [
                        'pickup_date' => $dTd_delivery_orders->pickup_date,
                        'pickup_time' => $dTd_delivery_orders->pickup_time,
                        'pickup_mobile' => $dTd_delivery_orders->pickup_mobile,
                        'transportation_type' => $dTd_delivery_orders->transportation_type,
                        'droupup_mobile'=> $dTd_delivery_orders->droupup_mobile
                 ];
               }
                array_push($items, $item);
           
            $Pickup_addressDtl = DB::table('dTd_pickup_address_dtls')->where('id',$dTd_delivery_orders->pickup_addressId)->first();
            $Dropup_addressDtl = DB::table('dTd_drop_address_dtls')->where('id',$dTd_delivery_orders->droupup_addressId)->first();
            $Pickup_itemDtl = DB::table('dTd_pickup_dtls')->where('id',$dTd_delivery_orders->pickup_itemId)->first();
            $pickup_items = [];
                
            $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($Pickup_itemDtl->image)).time()."cat." .$Pickup_itemDtl->image;
            //$request->pickup_item_image->move(config('constants.category_icon_image'), $file_path);
            
              if(isset($Pickup_itemDtl) ) { 
                 $item = [
                        'item_name' => $Pickup_itemDtl->item_name,
                        'item_detail' => $Pickup_itemDtl->item_detail,
                        'quantity' => $Pickup_itemDtl->quantity,
                        'image' => env('APP_URL').config('constants.category_icon_image'). $Pickup_itemDtl->image,
                 ];
               }
                array_push($pickup_items, $item);
            
            $latitude1 = $Pickup_addressDtl->latitude;$longitude1 = $Pickup_addressDtl->longitude;
            $latitude2 = $Dropup_addressDtl->latitude;$longitude2 = $Dropup_addressDtl->longitude;$unit = 'miles';
            
              $theta = $longitude1 - $longitude2; 
              $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta))); 
              $distance = acos($distance); 
              $distance = rad2deg($distance); 
              $distance = $distance * 60 * 1.1515; 
              switch($unit) { 
                case 'miles': 
                  break; 
                case 'kilometers' : 
                  $distance = $distance * 1.609344; 
              } 
              $Distance =  (round($distance,2)); 
            
            $charge = \App\Charge::first();
            if($request->transportation_type == 'Two Wheeler') {
                
                $basic_kilometer = $charge->dTd_twoWheeler_basicKM;
                $basic_charge = $charge->dTd_twoWheeler_basicCharge;
                $extra_charges = $charge->dTd_twoWheeler_extraCharge;
                
            } 
            else if($request->transportation_type == 'Three Wheeler') { 
                
                $basic_kilometer = $charge->dTd_threeWheeler_basicKM;
                $basic_charge = $charge->dTd_threeWheeler_basicCharge;
                $extra_charges = $charge->dTd_threeWheeler_extraCharge;
                
            } 
            else if($request->transportation_type == 'Four Wheeler') {
                //->where('vehicle_type',$request->four_wheelervehicle_type)
                $FourWheelerCharges = DB::table('dTd_charges')->first();
                
                $basic_kilometer = $FourWheelerCharges->basic_km;
                $basic_charge = $FourWheelerCharges->basic_charge;
                $extra_charges = $FourWheelerCharges->extra_charge;
                
            } 
            else { $extra_charges = 0; }
            
            if($Distance > $basic_kilometer)
            {
               $Delivery_charge = $Distance * $basic_charge;
            }
            else
            {
                $basic_chargeCalc =  $basic_kilometer * $basic_charge;
                $Extra_KM = $Distance - $basic_kilometer;
                $extra_chargeCalc = $Extra_KM * $extra_charges;
                $Delivery_charge = $basic_chargeCalc + $extra_chargeCalc;
            }
            
            $tax=0; $grand_total = 1;
            //'grand_total' => $grand_total,$grand_total = $Delivery_charge + $tax
            DB::table('door_to_doorDelivery')->where('order_id',$order_id)->update(array('distance' => $Distance,'tax' => 0,'grand_total' => $grand_total)); 
            
            // $user = \App\User::where('id', auth('api')->user()->id)->where('active', 1)->first();
           //   $CappData = [
           //     'fcm' => $user->fcm,
             //   'title' => config('constants.order_placed.title'),
             //   'body' => 'Vdeliverz Door To Door Delivery, Hurray ! You order has been placed, Thanks for shopping with Vdeliverz. Shop more and avail more coupons.',
             //   'icon' => '',
             //   'type' => 1,
            //];

            //sendSingleAppNotification($CappData, env('CUS_FCM'));
            
            
            return response()->json(['status' => true, 'message' => 'Door To Door delivery booking completed','order_id' => $order_id,'dtd_deliverydtl' =>$items, 'pickup_address' => $Pickup_addressDtl,'droup_address' => $Dropup_addressDtl,'items_dtl'=>$pickup_items,'delivery_charge'=>round($Delivery_charge),
            'tax' => $tax, 'grand_total' => round($grand_total)]);
            
        }
        catch (\Throwable $th) {
           // return apiCatchResponse();
            dd($th);
        }
    }
    
    public function dtd_payment_detail(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required',
                'payment_status'=>'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            DB::beginTransaction();
                
           
           $dTd_delivery_orders = DB::table('door_to_doorDelivery')->where('order_id',$request->order_id)->first();
            
           $items = [];$pickup_items=[];
           if(isset($dTd_delivery_orders) ) { 
             $item = [
                    'pickup_date' => $dTd_delivery_orders->pickup_date,
                    'pickup_time' => $dTd_delivery_orders->pickup_time,
                    'pickup_mobile' => $dTd_delivery_orders->pickup_mobile,
                    'transportation_type' => $dTd_delivery_orders->transportation_type,
                    'droupup_mobile'=> $dTd_delivery_orders->droupup_mobile,
             ];
           }
            array_push($items, $item);
            
            $Pickup_addressDtl = DB::table('dTd_pickup_address_dtls')->where('id',$dTd_delivery_orders->pickup_addressId)->first();
            $Dropup_addressDtl = DB::table('dTd_drop_address_dtls')->where('id',$dTd_delivery_orders->droupup_addressId)->first();
            $Pickup_itemDtl = DB::table('dTd_pickup_dtls')->where('id',$dTd_delivery_orders->pickup_itemId)->first();
            if(isset($Pickup_itemDtl) ) { 
                 $item = [
                        'item_name' => $Pickup_itemDtl->item_name,
                        'item_detail' => $Pickup_itemDtl->item_detail,
                        'quantity' => $Pickup_itemDtl->quantity,
                        'image' => env('APP_URL').config('constants.category_icon_image'). $Pickup_itemDtl->image,
                 ];
               }
                array_push($pickup_items, $item);
            
             $charge = \App\Charge::first();
             DB::commit();
            
             return response()->json(['status'=> true,'order_id' => $request->order_id,'dtd_deliverydtl' =>$items, 'pickup_address' => $Pickup_addressDtl,'droup_address' => $Dropup_addressDtl,
             'items_dtl'=>$pickup_items,'delivery_charge'=>round($dTd_delivery_orders->distance * $charge->dTd_extracharge),'tax' => $dTd_delivery_orders->tax, 
             'grand_total' => round($dTd_delivery_orders->grand_total)]);
        } catch (\Throwable $th) {
            dd($th);
        }
    }
    
    public function dTd_orderPlaced(Request $request){
        try
        {
             $validator = Validator::make($request->all(), [
                'order_id'=>'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            
            
           $dTd_delivery_orders = DB::table('door_to_doorDelivery')->where('order_id',$request->order_id)->first();
            
           $items = [];$pickup_items=[];
           if(isset($dTd_delivery_orders) ) { 
             $item = [
                    'pickup_date' => $dTd_delivery_orders->pickup_date,
                    'pickup_time' => $dTd_delivery_orders->pickup_time,
                    'pickup_mobile' => $dTd_delivery_orders->pickup_mobile,
                    'transportation_type' => $dTd_delivery_orders->transportation_type,
                    'droupup_mobile'=> $dTd_delivery_orders->droupup_mobile,
             ];
           }
            array_push($items, $item);
            
            $Pickup_addressDtl = DB::table('dTd_pickup_address_dtls')->where('id',$dTd_delivery_orders->pickup_addressId)->first();
            $Dropup_addressDtl = DB::table('dTd_drop_address_dtls')->where('id',$dTd_delivery_orders->droupup_addressId)->first();
            $Pickup_itemDtl = DB::table('dTd_pickup_dtls')->where('id',$dTd_delivery_orders->pickup_itemId)->first();
            if(isset($Pickup_itemDtl) ) { 
                 $item = [
                        'item_name' => $Pickup_itemDtl->item_name,
                        'item_detail' => $Pickup_itemDtl->item_detail,
                        'quantity' => $Pickup_itemDtl->quantity,
                        'image' => env('APP_URL').config('constants.category_icon_image'). $Pickup_itemDtl->image,
                 ];
               }
                array_push($pickup_items, $item);
            
            $charge = \App\Charge::first();
            //1-Order Placed
            DB::table('door_to_doorDelivery')->where('order_id',$request->order_id)->update(array('order_status' => 1)); 
            
            
             return response()->json(['status' => true, 'message' => 'Order Placed', 'dtd_deliverydtl' =>$items, 'pickup_address' => $Pickup_addressDtl,'droup_address' => $Dropup_addressDtl,'items_dtl'=>$Pickup_itemDtl,
             'delivery_charge'=>round($dTd_delivery_orders->distance * $charge->dTd_extracharge),'tax' => $dTd_delivery_orders->tax, 'grand_total' => round($dTd_delivery_orders->grand_total)]);
        }
        catch (\Throwable $th) {
            return apiCatchResponse();
            dd($th);
        }
    }
    
      public function dtd_myOrders(){
        try {
            $user = auth('api')->user();
            $user->id = 3849;
            $total_orders = DB::table('door_to_doorDelivery')->where('user_id', $user->id)->get();
            $orders = [];$items = [];$pickup_items=[];
            foreach ($total_orders as $key => $value) {
                   DB::beginTransaction();
           
                   $dTd_delivery_orders = DB::table('door_to_doorDelivery')->where('order_id',$value->order_id)->first();
                   $Pickup_addressDtl = DB::table('dTd_pickup_address_dtls')->where('id',$dTd_delivery_orders->pickup_addressId)->first();
                   $Dropup_addressDtl = DB::table('dTd_drop_address_dtls')->where('id',$dTd_delivery_orders->droupup_addressId)->first();
                   $Pickup_itemDtl = DB::table('dTd_pickup_dtls')->where('id',$dTd_delivery_orders->pickup_itemId)->first();
                   $dtd_order_status = DB::table('dTd_order_status')->where('id',$dTd_delivery_orders->order_status)->first();
                   $charge = \App\Charge::first();
                    
                   
                   if(isset($dTd_delivery_orders) ) { 
                     $item = [
                            'order_id' => $value->order_id,
                            'pickup_date' => $dTd_delivery_orders->pickup_date,
                            'pickup_time' => $dTd_delivery_orders->pickup_time,
                            'pickup_mobile' => $dTd_delivery_orders->pickup_mobile,
                            'transportation_type' => $dTd_delivery_orders->transportation_type,
                            'droupup_mobile'=> $dTd_delivery_orders->droupup_mobile,
                            'order_created_date' =>\Carbon\Carbon::parse($dTd_delivery_orders->created_date)->format('d-m-Y H:i:s A'),
                            'item_name' => $Pickup_itemDtl->item_name,
                            'item_detail' => $Pickup_itemDtl->item_detail,
                            'quantity' => $Pickup_itemDtl->quantity,
                            'image' => env('APP_URL').config('constants.category_icon_image'). $Pickup_itemDtl->image,
                            'pickup_address'=>	$Pickup_addressDtl->address,
                            'pickup_address_area'=>	$Pickup_addressDtl->area,
                            'pickup_address_landmark'=>	$Pickup_addressDtl->landmark,
                            'pickup_address_address_type'=>	$Pickup_addressDtl->address_type,
                            'pickup_address_latitude'=>	$Pickup_addressDtl->latitude,
                            'pickup_address_longitude'=>	$Pickup_addressDtl->longitude,
                            'pickup_address_phone_number'=>	$Pickup_addressDtl->phone_number,
                            'drop_address'=>	$Dropup_addressDtl->address,
                            'drop_address_area'=>	$Dropup_addressDtl->area,
                            'drop_address_landmark'=>	$Dropup_addressDtl->landmark,
                            'drop_address_address_type'=>	$Dropup_addressDtl->address_type,
                            'drop_address_latitude'=>	$Dropup_addressDtl->latitude,
                            'drop_address_longitude'=>	$Dropup_addressDtl->longitude,
                            'drop_address_phone_number'=>	$Dropup_addressDtl->phone_number,
                            'order_status' => $dTd_delivery_orders->order_status == null ? '0' : $dTd_delivery_orders->order_status,
                            'delivery_charge' =>round($dTd_delivery_orders->distance * $charge->dTd_extracharge),
                            'tax' => $dTd_delivery_orders->tax,
                            'grandTotal' =>round($dTd_delivery_orders->grand_total)
                     ];
                   }
                   array_push($items, $item);
            }
            
            
            return response()->json(['status' => true, 'message' => 'User Orders', 'orders' => $items]);
        } catch (\Throwable $th) {
            //return apiCatchResponse();
            dd($th);
        }
    }
    
     public function dTd_trackOrder(Request $request){

        try {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required',
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            $dTd_delivery_orders = DB::table('door_to_doorDelivery')->where('order_id',$request->order_id)->first();
            $Pickup_addressDtl = DB::table('dTd_pickup_address_dtls')->where('id',$dTd_delivery_orders->pickup_addressId)->first();
            $Dropup_addressDtl = DB::table('dTd_drop_address_dtls')->where('id',$dTd_delivery_orders->droupup_addressId)->first();
            $Pickup_itemDtl = DB::table('dTd_pickup_dtls')->where('id',$dTd_delivery_orders->pickup_itemId)->first();
            $dtd_order_status = DB::table('dTd_order_status')->where('id',$dTd_delivery_orders->order_status)->first();
            $charge = \App\Charge::first();
            
            $items = [];
            
              $item = [
                    'pickup_date' => $dTd_delivery_orders->pickup_date,
                    'pickup_time' => $dTd_delivery_orders->pickup_time,
                    'pickup_mobile' => $dTd_delivery_orders->pickup_mobile,
                    'transportation_type' => $dTd_delivery_orders->transportation_type,
                    'droupup_mobile'=> $dTd_delivery_orders->droupup_mobile,
                    'item_name' => $Pickup_itemDtl->item_name,
                    'item_detail' => $Pickup_itemDtl->item_detail,
                    'quantity' => $Pickup_itemDtl->quantity,
                    'image' => env('APP_URL').config('constants.category_icon_image'). $Pickup_itemDtl->image,
                    'pickup_address'=>	$Pickup_addressDtl->address,
                    'pickup_address_area'=>	$Pickup_addressDtl->area,
                    'pickup_address_landmark'=>	$Pickup_addressDtl->landmark,
                    'pickup_address_address_type'=>	$Pickup_addressDtl->address_type,
                    'pickup_address_latitude'=>	$Pickup_addressDtl->latitude,
                    'pickup_address_longitude'=>	$Pickup_addressDtl->longitude,
                    'pickup_address_phone_number'=>	$Pickup_addressDtl->phone_number,
                    'drop_address'=>	$Dropup_addressDtl->address,
                    'drop_address_area'=>	$Dropup_addressDtl->area,
                    'drop_address_landmark'=>	$Dropup_addressDtl->landmark,
                    'drop_address_address_type'=>	$Dropup_addressDtl->address_type,
                    'drop_address_latitude'=>	$Dropup_addressDtl->latitude,
                    'drop_address_longitude'=>	$Dropup_addressDtl->longitude,
                    'drop_address_phone_number'=>	$Dropup_addressDtl->phone_number,
                    'delivery_charge' =>round($dTd_delivery_orders->distance * $charge->dTd_extracharge),
                    'tax' => $dTd_delivery_orders->tax,
                    'grandTotal' =>round($dTd_delivery_orders->grand_total),
                    'payment_status'=> $dTd_delivery_orders->payment_status == null ? '' : $dTd_delivery_orders->payment_status
             ];
            array_push($items, $item);
            
            
            $time_details = [
                'order_status' => $dTd_delivery_orders->order_status == null ? '0' : $dTd_delivery_orders->order_status,
                'confirmed_at' => \Carbon\Carbon::parse($dTd_delivery_orders->created_date)->format('d-m-Y H:i:s A'),
            ];
            
            
            if($dTd_delivery_orders->order_status == 2){
                $user = \App\User::where('id', $dTd_delivery_orders->delivery_boy)->first();
                $delivery_boy = $user->mobile;
            }
            
            
        
            
            return response()->json(['status' => true, 'message' => 'Track Order', 'order_details' => $items,'time_details' => $time_details, 'delivery_boy_contact' => $delivery_boy ?? null ]);
        } catch (\Throwable $th) {
           // return apiCatchResponse();
            dd($th);
        }
    }
    
      public function dtd_payment_initiate(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
         
           
                $local_order = DB::table('door_to_doorDelivery')->where('order_id',$request->order_id)->first();
                if(!$local_order){
                    return response(['status' => false, 'message' => 'Invalid Order!']);
                }
                $total = $local_order->grand_total;
              
                $rand = rand(1000, 9999);
                $ldate = date('YmdHis');
                $total_amount = str_replace(',','',number_format($total,2));
                $paytmParams = array();
                
    
                $paytmParams["body"] = array(
                    "requestType"   => "Payment",
                    "mid"           => env('PAYTM_MERCHANT_ID'),
                    "websiteName"   => env('PAYTM_MERCHANT_WEBSITE'),
                    "orderId"       => $request->order_id.$ldate,
                    "callbackUrl"   => env('PAYTM_CALLBACK').$request->order_id.$ldate,
                    //"callbackUrl"   => "https://vdeliverz.gdigitaldelivery.com/callback.php",
                    "txnAmount"     => array(
                        "value"     => $total_amount,
                        "currency"  => "INR",
                    ),
                    "userInfo"      => array(
                        "custId"    => "CUST_001".$rand,
                    ),
                 
                );
               
                $checksum = generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), env('PAYTM_MERCHANT_KEY'));
                $paytmParams["head"] = array(
                    "signature"    => $checksum
                );
                
                $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);
                
    
                $url = env('PAYTM_API_URL').env('PAYTM_MERCHANT_ID')."&orderId=".$request->order_id.$ldate;
              
                //dump($url);
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json")); 
                $response = json_decode(curl_exec($ch));
                //dd($response->body);
                
                $order = DB::table('door_to_doorDelivery')->where('order_id',$request->order_id)->update(array('payment_type'=>'PAYTM'));
                
                if($response->body->resultInfo->resultStatus == 'S'){
                     return response()->json(['status' => true, 'message' => $response->body->resultInfo->resultMsg, 'order_id' => $local_order->order_id,'token' => $response->body->txnToken,
                     'mid' => env('PAYTM_MERCHANT_ID'),'pay_amount' => $total, 'order_id' => $request->order_id.$ldate]);
                }else{
                     return response()->json(['status' => true, 'message' => $response->body->resultInfo->resultMsg, 'order_id' => $local_order->order_id,'mid' => env('PAYTM_MERCHANT_ID')]);
                }
           
            
            
             return response()->json(['status' => true, 'message' => 'Paytm order token Created', 'order_id' => $local_order->order_id,'token' => $response->body->txnToken,'mid' => env('PAYTM_MERCHANT_ID')]);
        } catch (\Throwable $th) {
            dd($th);
        }
    }
    
    
    public function dtd_payment_callback(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required',
                'payment_id'=>'required',
                'payment_status'=>'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            DB::beginTransaction();
            //return $request->all();
            
            
           $order = DB::table('door_to_doorDelivery')->where('order_id',$request->order_id)
                ->update(array('payment_id'=>$request->payment_id,'payment_status' => json_encode($request->payment_status)));
                
           
           $dTd_delivery_orders = DB::table('door_to_doorDelivery')->where('order_id',$request->order_id)->first();
            
           $items = [];$pickup_items=[];
           if(isset($dTd_delivery_orders) ) { 
             $item = [
                    'pickup_date' => $dTd_delivery_orders->pickup_date,
                    'pickup_time' => $dTd_delivery_orders->pickup_time,
                    'pickup_mobile' => $dTd_delivery_orders->pickup_mobile,
                    'transportation_type' => $dTd_delivery_orders->transportation_type,
                    'droupup_mobile'=> $dTd_delivery_orders->droupup_mobile,
             ];
           }
            array_push($items, $item);
            
            $Pickup_addressDtl = DB::table('dTd_pickup_address_dtls')->where('id',$dTd_delivery_orders->pickup_addressId)->first();
            $Dropup_addressDtl = DB::table('dTd_drop_address_dtls')->where('id',$dTd_delivery_orders->droupup_addressId)->first();
            $Pickup_itemDtl = DB::table('dTd_pickup_dtls')->where('id',$dTd_delivery_orders->pickup_itemId)->first();
            if(isset($Pickup_itemDtl) ) { 
                 $item = [
                        'item_name' => $Pickup_itemDtl->item_name,
                        'item_detail' => $Pickup_itemDtl->item_detail,
                        'quantity' => $Pickup_itemDtl->quantity,
                        'image' => env('APP_URL').config('constants.category_icon_image'). $Pickup_itemDtl->image,
                 ];
               }
                array_push($pickup_items, $item);
                
            
            
            $order_dtl = DB::table('door_to_doorDelivery')->where('order_id',$request->order_id)->first();
            
             $charge = \App\Charge::first();
            DB::commit();
             return response()->json(['status'=> true,'message','Payment Callback','dtd_deliverydtl' =>$items,'pickup_address' => $Pickup_addressDtl,'droup_address' => $Dropup_addressDtl,'items_dtl'=>$pickup_items,
             'delivery_charge'=>round($dTd_delivery_orders->distance * $charge->dTd_extracharge),'tax' => $dTd_delivery_orders->tax, 'grand_total' => round($dTd_delivery_orders->grand_total),
             'payment_status' =>$request->payment_status]);
        } catch (\Throwable $th) {
            dd($th);
        }
    }
    
        public function d2d_orderPicked(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            
            DB::beginTransaction();
            $order = DB::table('door_to_doorDelivery')->where('order_id',$request->order_id)->first();
            
            if($order != null)
            {
                $order = DB::table('door_to_doorDelivery')->where('order_id',$request->order_id)
                ->update(array('order_pickedDate'=>Carbon\Carbon::now(),'order_status' => 6));
                
                DB::commit();
            
                $CappData = [
                    'fcm' => $delivery->order->user->fcm,
                    'title' => config('constants.order_picked.title'),
                    'body' => 'Vdeliverz - Delivery, Your order is picked by '.$delivery->user->name.' from the vendor, you will receive it shortly',
                    'icon' => '',
                    'type' => 1
                ];
               // sendSingleAppNotification($VappData, env('VEN_FCM'));
                sendSingleAppNotification($CappData, env('CUS_FCM'));
                return response()->json(['status' => true, 'message' => 'Order Picked']);
            }
            else
            {
                 return response()->json(['status' => false, 'message' => 'Order not found']);
            }
            
        } catch (\Throwable $th) {
            DB::rollback();
            return apiCatchResponse();
            dd($th);
        }
    }
}
