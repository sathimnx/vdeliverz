<?php

namespace App\Http\Controllers\Api\v3;

use App\Order;
use App\Stock;
use Validator, DB, Auth;
use Illuminate\Http\Request;
use App\Events\PostPublished;
use App\Http\Controllers\Controller;
use App\Http\Resources\StockResource;

class BaseController extends Controller
{
    public function dashboard(){
        try {
            $categories = categoriesList();
            $cat1 = $categories->splice(4);
            $notify = auth('api')->user() ? auth('api')->user()->notify_count : 0;
            $wish = auth('api')->user() ? auth('api')->user()->wishlist_count : 0;
            $banners = appBanners();
            $contact = \App\Contact::first(['mobile', 'wp as whatsapp', 'email']);
            $shop_banners = \App\ShopBanner::where('active', 1)->inRandomOrder()->limit(8)->get(['id as shop_banner_id', 'image']);
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
                $data = canShowShop($request->address_id, $shop);
                if($data['int_dis'] <= $shop->radius && $shop->active == 1){
                    $shops[] = [
                    'shop_id' => $shop->id,
                    'shop_name' => $shop->name,
                    'shop_image' => $shop->image,
                    'shop_area' => $shop->area,
                    'shop_address' => $shop->street.', '.$shop->area.', '.$shop->city,
                    'price' => number_format($shop->price, 2).' '.$shop->currency,
                    'distance' => $data['distance'],
                    'time' => $data['time'],
                    'rating' => $shop->rating_avg,
                    'rating_count' => $shop->rating_count,
                    "is_opened" => $shop->is_opened
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

     //Get products List for a category
     public function products(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'shop_id' => 'required',
                // 'latitude' => 'required',
                // 'longitude' => 'required',
                'device_id' => 'required',
                'shop_category_id' => 'required',
                'address_id' => 'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            $shop = \App\Shop::where('id', $request->shop_id)->where('active', 1)->first();
            if(!$shop){
                return response()->json(['status' => false, 'message' => 'No shops Found!']);
            }
            $data = canShowShop($request->address_id, $shop);

            $shop_details = [
                'shop_id' => $shop->id,
                'shop_name' => $shop->name,
                'shop_image' => $shop->image,
                'shop_area' => $shop->area,
                'shop_address' => $shop->street.', '.$shop->area.', '.$shop->city,
                'distance' => $data['distance'],
                'time' => $data['time'],
                'price' => number_format($shop->price, 2).' '.$shop->currency,
                'rating' => $shop->rating_avg,
                'rating_count' => $shop->rating_count,
                'is_wishlist' => $shop->wishlist(),
                'is_cart' => $shop->cart($request->device_id),
                "is_opened" => $shop->is_opened
            ];

            $items = [];
            $subCategories = [];
            $products = \App\Product::where('shop_id', $request->shop_id)->where('active', 1)->where('rec', 1)->where('category_id', $request->shop_category_id)->with('stocks', 'shop', 'subCategory')->inRandomOrder()->orderBy('updated_at', 'desc')->get()->take(8);
            if($products->isEmpty()){
                $products = \App\Product::where('shop_id', $request->shop_id)->where('active', 1)->where('category_id', $request->shop_category_id)->with('stocks', 'shop', 'subCategory')->inRandomOrder()->orderBy('updated_at', 'desc')->get()->take(8);
            }
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
                    'variants' => $cart_var['variants']
                ];
                if(count($cart_var['variants']) > 0){
                array_push($items, $item);
                }
            }
            foreach ($shop->productCategories()->where('shop_sub_category.active', 1)->orderBy('order')->get() as $key => $category) {
                $cc = $category->products()->where('shop_id', $request->shop_id)->where('active', 1)->get();
                if($cc->isNotEmpty()){
                    $subCategories[] = [
                        'category_id' => $category->id,
                        'category_name' => $category->name,
                    ];
                }
            }
            return response()->json(['status' => true, 'message' => 'Products List', 'shop_details' => $shop_details, 'categories' => $subCategories ?? [], 'shop_products' => $items]);
        } catch (\Throwable $th) {
           return apiCatchResponse();
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
                    'variants' => [$cart_var['variants'][0]]
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
            'to' => 'dY-JkAxdSDW7CRHpkuy6tc:APA91bFCgsyVsh92fK7yi1w-U2fw-9cddnvG6bk4pjSZ73ur8P9PuZNZ0NGY7wqclB9tegpfmPrJ9jkQnCh4qvgwK5x95QgFGZAXn1g6NLEfeoBRLRnET9auX-DC593tmiRjNbSrq3Kj',
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
}
