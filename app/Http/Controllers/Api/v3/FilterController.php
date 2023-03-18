<?php

namespace App\Http\Controllers\Api\v3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DB, Validator, Auth;
use App\Http\Resources\ShopResource;
use App\Http\Resources\ProductResource;

class FilterController extends Controller
{
    public function cuisines(){
        $cuisines = \App\Cuisine::where('active', 1)->orderBy('name', 'asc')->get(['id as cuisine_id', 'name as cuisine_name']);
        return response()->json(['status' => true, 'message' => 'Cuisines List', 'cuisines' => $cuisines]);
    }

    public function filter(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                "filter_type" => "required|integer",
                'shop_category_id' => 'required',
                'device_id' => 'required',
                'rating' => 'required_if:filter_type,6|integer',
                'cuisine_id' => 'required_if:filter_type,5',
                'price' => 'required_if:filter_type,7',
                // 'latitude' => 'required',
                // 'longitude' => 'required'
                'address_id' => 'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            // filter_type 8 - Open now
            // filter_type 10 - A-Z
            // filter_type 11 - Z-A
            $day = now()->format('l');
            $time = now()->format('H:i:s');
            if($request->filter_type == 8){
                    $shops = \App\Shop::where('opened', 1)->whereRaw("FIND_IN_SET('".$day."', weekdays)")
                    ->where('opens_at', '<=', $time)
                    ->where('closes_at', '>=', $time)->whereHas('categories', function($query) use($request){
                    $query->where('category_id', $request->shop_category_id);
                })->get();
            }else{
                $shops = \App\Shop::whereHas('categories', function($query) use($request){
                    $query->where('category_id', $request->shop_category_id);
                })->get();
            }
            if($request->filter_type == 10){
                $shops = \App\Shop::whereHas('categories', function($query) use($request){
                $query->where('category_id', $request->shop_category_id);
            })->orderBy('name', 'asc')->get();
            }
            if($request->filter_type == 11){
                $shops = \App\Shop::whereHas('categories', function($query) use($request){
                $query->where('category_id', $request->shop_category_id);
            })->orderBy('name', 'desc')->get();
            }
            if($request->filter_type == 1){
                $shops = \App\Shop::whereHas('categories', function($query) use($request){
                $query->where('category_id', $request->shop_category_id);
            })->orderBy('rating_avg', 'desc')->get();
            }

            if($request->filter_type == 2){
                $shops = \App\Shop::whereHas('categories', function($query) use($request){
                $query->where('category_id', $request->shop_category_id);
            })->orderBy('price', 'desc')->get();
            }
            if($request->filter_type == 3){
                $shops = \App\Shop::whereHas('categories', function($query) use($request){
                $query->where('category_id', $request->shop_category_id);
            })->orderBy('price', 'asc')->get();
            }

            if($request->filter_type == 6){
                $shops = \App\Shop::whereHas('categories', function($query) use($request){
                $query->where('category_id', $request->shop_category_id);
            })->where('rating_avg', '>=', $request->rating)->get();
            }
            if($request->filter_type == 7){
                $shops = \App\Shop::whereHas('categories', function($query) use($request){
                $query->where('category_id', $request->shop_category_id);
            })->where('price', '>=', $request->price)->get();
            }

            if($shops->isEmpty()){
                return response()->json(['status' => false, 'message' => 'No Shops Found']);
            }
            $filter_datas1 = [];

            foreach ($shops as $k => $shop) {
                $data = canShowShop($request->address_id, $shop);
                if($data['int_dis'] <= $shop->radius && $shop->active == 1){

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

                }
            }
            $quick = [];
            // $similar = \App\Product::where('category_id', $request->shop_category_id)->with('stocks')->inRandomOrder()->limit(6)->get();
            foreach ($shop_products->products()->where('category_id', $request->shop_category_id)->take(7)->get() as $key => $item) {
                $car_var = getVariant($item->stocks, $request->device_id);
                $single = [
                    'product_id' => $item->id,
                    'product_name' => $item->name,
                    'product_image' => $item->image,
                    'description' => $item->description,
                    'variety' => $item->variety,
                    'variants' => $car_var['variants']
                ];
                array_push($quick, $single);
            }

            if($request->filter_type == 4){
                $filter_datas1 = collect($filter_datas1)->sortBy('int_dis')->toArray();
//                $filter_datas2 = collect($filter_datas2)->sortBy('int_dis')->toArray();
            }
            // $res_data = array_chunk($filter_datas1, 2);
            // dd($filter_datas1, $res_data);
            $result_shops1 = collect($filter_datas1)->take(2) ?? [];
            $result_shops2 = collect($filter_datas1)->splice(2) ?? [];
            return response()->json(['status' => true, 'message' => 'Filter Result', 'shop_category_id' => $request->shop_category_id, 'shops' => $result_shops1, 'shops1' => $result_shops2, 'products' => $quick]);
        } catch (\Throwable $th) {
        //    return apiCatchResponse();
            dd($th);
        }
    }

    public function multiFilter(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'shop_category_id' => 'required',
                'device_id' => 'required',
                // 'latitude' => 'required',
                // 'longitude' => 'required'
                'address_id' => 'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            // Popularity - append 1 to filter_types array
            // High to Low - append 2 to filter_types array
            // Low to High - append 3 to filter_types array
            // Delivery Time - append 4 to filter_types array
            // Cuisines - append 5 to filter_types array with cuisine_ids - [1, 2]
            // Rating - append 6 to filter_types array with "rating":2
            // Price - append 7 to filter_types array with "price":2
            $sort = $request->sort;
            $day = now()->format('l');
            $time = now()->format('H:i:s');
            $shops = \App\Shop::whereHas('categories', function($query) use($request){
                $query->where('category_id', $request->shop_category_id);
            });
            // dd($shops->get());
            if($sort == 1){
                $shops = $shops->orderBy('rating_avg', 'desc');
            }
            if($sort == 2){
                $shops = $shops->orderBy('price', 'desc');
            }
            if($sort == 3){
                $shops = $shops->orderBy('price', 'asc');
            }
            if(isset($request->rating) && $request->rating != null){
                $shops = $shops->where('rating_avg', '>=', $request->rating)->orderBy('rating_avg', 'asc');
                // dd($shops->get());
            }
            if(isset($request->price) && $request->price == 1){
                $shops = $shops->where('price', '<', 3);
            }
            if(isset($request->price) && $request->price == 2){
                $shops = $shops->whereBetween('price', [3, 5]);

            }
            if(isset($request->price) && $request->price == 3){
                $shops = $shops->whereBetween('price', [5, 8]);
            }
            if(isset($request->price) && $request->price == 4){
                $shops = $shops->where('price', '>', 8);
            }
            // dd($shops->get());
            // if(in_array(123, $request->filter_types)){
            //     $shops_list = shopsList($shops->get(), $request);
            //     $shops_list = collect($shops_list['merged'])->sortBy('int_dis')->toArray();
            // }else{
                $shops_list = shopsList($shops->get(), $request);
            // }
            if($sort == 4){

                $shops_list['merged'] = collect($shops_list['merged'])->sortBy('int_dis')->toArray();
            }
            $result_shops1 = collect($shops_list['merged'])->take(2) ?? [];
            $result_shops2 = collect($shops_list['merged'])->splice(2) ?? [];
            // $similar = \App\Product::where('category_id', $request->shop_category_id)->with('stocks')->inRandomOrder()->limit(6)->get();
            return response()->json(['status' => true, 'message' => 'Filter Result', 'shop_category_id' => $request->shop_category_id, 'shops' => $result_shops1, 'shops1' => $result_shops2, 'products' => isset($shops_list['products']) ? quickGrab($shops_list['products'], $request) : []]);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            dd($th);
        }
    }

    public function productSearch(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'search' => 'required',
                'device_id' => 'required',
                // "latitude" => 'required',
                // "longitude" => 'required',
                'address_id' => 'required'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            if(isset($request->category_id)){
                $products = \App\Product::where('name', 'Like', '%'.$request->search.'%')->where('category_id', $request->category_id)->where('active', 1)->get();
            }else{
                $products = \App\Product::where('name', 'Like', '%'.$request->search.'%')->where('active', 1)->get();
            }
            if(isset($request->shop_id) && isset($request->category_id)){
                $products = \App\Product::where('name', 'Like', '%'.$request->search.'%')->where('category_id', $request->category_id)->where('shop_id', $request->shop_id)->where('active', 1)->get();
            }
            $items = [];
            foreach ($products as $key => $product) {
                $data = canShowShop($request->address_id, $product->shop);
                if($data['int_dis'] <= $product->shop->radius && $product->shop->active == 1){
                    $pro_var = getVariant($product->stocks, $request->device_id);
                $item = [
                    'product_id' => $product->id,
                    "category_id" => $product->category->id,
                    'product_name' => $product->name,
                    'product_image' => $product->image,
                    'description' => $product->description,
                    'variety' => $product->variety,
                    'variants' => $pro_var['variants']
                ];
                array_push($items, $item);
                }
            }

            return response()->json(['status' => true, 'message' => 'Product search Result', 'products' => $items]);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            dd($th);
        }
    }

}
