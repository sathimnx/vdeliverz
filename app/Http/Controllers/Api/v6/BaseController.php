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
                     $coupons = \App\VendorCoupon::where('shop_id', $request->id)->whereDate('expired_on', '>=', now())->get()->count();
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
                'id' => 'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            
            if(\App\VendorCoupon::whereDate('expired_on', '>=', now())->where('shop_id', 0)->get()->count() > 0)
            {
                 $coupons = \App\VendorCoupon::whereDate('expired_on', '>=', now())->where('shop_id', 0)->get();
            }
            else
            {
                 $coupons = \App\VendorCoupon::where('shop_id', $request->id)->whereDate('expired_on', '>=', now())->get();
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
            $products = \App\Product::where('shop_id', $request->shop_id)->where('active', 1)->where('category_id', $request->shop_category_id)->with('stocks', 'shop', 'subCategory')->orderBy('updated_at', 'desc')->get();
            $categories = $products->unique('sub_category_id');
            foreach ($products as $key => $product) {
                $cart_var = getVariant($product->stocks, $request->device_id);
                $item = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_image' => $product->image,
                    'description' => $product->description,
                    'can_customize' => $product->can_customize,
                    'variety' => $product->variety,
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
                $cat = [
                    'category_id' => $category->sub_category_id,
                    'category_name' => $subCategor[0]->name,
                    'category_count' => \App\Product::where('sub_category_id', $category->sub_category_id)->where('shop_id', $request->shop_id)->count()
            ];
                array_push($subCategories, $cat);
            }
              //$subCategories =array_reverse($subCategories);
             if(\App\VendorCoupon::whereDate('expired_on', '>=', now())->where('shop_id', 0)->get()->count() > 0)
            {
                 $coupons = \App\VendorCoupon::whereDate('expired_on', '>=', now())->where('shop_id', 0)->get();
            }
            else
            {
                 $coupons = \App\VendorCoupon::where('shop_id', $request->id)->whereDate('expired_on', '>=', now())->get();
            }
            return response()->json(['status' => true, 'message' => 'Products List', 'recommended_count' => $products->count(), 'shop_details' => $shop_details,
            'coupons' => $coupons, 'categories' => $subCategories, 'shop_products' => collect($items)->take(15)]);
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
            $items = [];
            $products = \App\Product::where('sub_category_id', $request->category_id)->where('shop_id', $request->shop_id)->where('active', 1)->with('stocks', 'shop', 'subCategory')->get();
            
            if($products->isEmpty()){
                return response()->json(['status' => false, 'message' => 'No Products Found!']);
            }
            $categories = $products->unique('sub_category_id');
            foreach ($products as $key => $product) {
                $cart_var = getVariant($product->stocks, $request->device_id);
                
                $item = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_image' => $product->image,
                    'description' => $product->description,
                    'variety' => $product->variety,
                    'can_customize' => $product->can_customize,
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
            $collection = \App\Shop::where('shops.active', 1)->join('products', 'shops.id', '=', 'products.shop_id')->where('category_id', $cat_id)->orderBy('prior')->get();


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
                     $coupons = \App\VendorCoupon::where('shop_id', $request->id)->whereDate('expired_on', '>=', now())->get()->count();
                }
             //  $data = canShowShop($request->address_id, $shop);
             //   if($data['int_dis'] <= $shop->radius && $shop->active == 1){
                    $shops[] = [
                        'shop_id' => $shop->id,
                        'shop_name' => $shop->name,
                        'shop_image' => $shop->image,
                        'shop_area' => $shop->area,
                        'shop_address' => $shop->street.', '.$shop->area.', '.$shop->city,
                        'price' => number_format($shop->price, 2).' '.$shop->currency,
                       // 'distance' => $data['distance'],
                       // 'time' => $data['time'],
                       'cat_id'=>$shop->category_id,
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
         //   }
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
           // return ['status' => false, 'message' => "Can't able to fetch distance!"];
            dd($th);
        }
    }
}
