<?php

namespace App\Http\Controllers\Api\vendor\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ProductListResource;
use App\Http\Resources\ProductDetailResource;
use Validator, DB;
use App\Product;
use App\Notifications\ProductNotification;
use Notification;

class ProductController extends Controller
{
    public function index(){
        $products = auth('api')->user()->products()->paginate(10);
        $data['pagination'][] = apiPagination($products);
        $data['data'] = $this->productsFormat($products);
        return response(['status' => true, 'message' => "Products List.", 'products' => $data]);
    }

    public function show(Request $request){
       try {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $product = \App\Product::find($request->product_id);
        if(!$product){
            return response(['status' => false, 'message' => 'Product Not Found.']);
        }
        foreach ($product->stocks as $key => $stock) {
            $stocks[] = [
                "stock_id" => $stock->id,
                "unit" => $stock->unit,
                "variant" => $stock->variant,
                "size" => $stock->size,
                "available" => $stock->available,
                "selling_price" => $stock->price,
                "actual_price" => $stock->actual_price
            ];
        }
        foreach ($product->toppings as $key => $topping) {
            $toppings[] = [
                "topping_id" => $topping->id,
                "name" => $topping->name,
                "price" => $topping->price,
                "available" => $topping->available,
                'title_id' => $topping->title_id,
                "variety" => $topping->variety,
            ];

        }
        $titles = \App\Title::get(['id as title_id', 'name']);
        $shops = \App\Shop::where('user_id', auth('api')->user()->id)->select('id as shop_id', 'name')->get();
        $categories = \App\Category::select('id as category_id', 'name')->get();
        $subCategories = \App\SubCategory::select('id as sub_category_id', 'name')->get();
        $cuisines = \App\Cuisine::select('id as cuisine_id', 'name')->get();
        return response(['status' => true, 'message' => 'Product Details', 'product' => new ProductDetailResource($product),
         'stocks' => $stocks ?? NULL,
         'toppings' => $toppings ?? NULL,
         'shops' => $shops, 'categories' => $categories, 'titles' => $titles, 'sub_categories' => $subCategories, 'cuisines' => $cuisines]);
       } catch (\Throwable $th) {
           dd($th);
       }
    }

    public function create(){
        $shops = \App\Shop::where('user_id', auth('api')->user()->id)->select('id as shop_id', 'name')->get();
        $categories = \App\Category::select('id as category_id', 'name')->get();
        $subCategories = \App\SubCategory::select('id as sub_category_id', 'name')->get();
        $cuisines = \App\Cuisine::select('id as cuisine_id', 'name')->get();
        return response(['status' => true, 'message' => 'Product Create',
        'shops' => $shops, 'categories' => $categories, 'sub_categories' => $subCategories, 'cuisines' => $cuisines]);
    }

    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'shop_id' => 'required',
            'category_id' => 'required',
            'sub_category_id' => 'required',
            'name' => 'required',
            'image' => 'image|max:1024',
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $product = \App\Product::find($request->product_id);
        if(!$product){
            return response(['status' => false, 'message' => 'Product Not Found.']);
        }
        $product->name = $request->name;
        $product->variety = $request->variety;
        $product->shop_id = $request->shop_id;
        $product->cuisine_id = $request->cuisine_id;
        $product->category_id = $request->category_id;
        $product->sub_category_id = $request->sub_category_id;
        $product->description = $request->description;
        if($request->hasFile('image')){
            if($request->file('image')->isValid())
            {
                $extension = $request->image->extension();
                $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->name)).time()."product." .$extension;
                $request->image->move(config('constants.product_image'), $file_path);
                $product->image = $file_path;
            }
        }
        $product->save();
        return response(['status' => true, 'message' => 'Product Updated.']);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'shop_id' => 'required',
            'category_id' => 'required',
            'sub_category_id' => 'required',
            'name' => 'required',
            'image' => 'required|image|max:1024',
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $sub_category_id = $request->sub_category_id;
        $prod_count = Shop::find($request->shop_id)->productCategories->count();
        $shop_cat = \App\ShopSubCategory::where('shop_id', $request->shop_id)->where('sub_category_id', $sub_category_id)->first();
            if(!$shop_cat){
                \App\ShopSubCategory::create(['shop_id' => $request->shop_id, 'sub_category_id' => $sub_category_id, 'order' => $prod_count + 1]);
            }
        $product = new Product;
        $product->name = $request->name;
        $product->variety = $request->variety;
        $product->shop_id = $request->shop_id;
        $product->cuisine_id = $request->cuisine_id;
        $product->category_id = $request->category_id;
        $product->sub_category_id = $request->sub_category_id;
        $product->description = $request->description;
        if($request->hasFile('image')){
            if($request->file('image')->isValid())
            {
                $extension = $request->image->extension();
                $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->name)).time()."product." .$extension;
                $request->image->move(config('constants.product_image'), $file_path);
                $product->image = $file_path;
            }
        }
        $product->save();
        return response(['status' => true, 'message' => 'Product Created.', 'product_id' => $product->id]);
    }

    public function categoryProducts(Request $request){
        $validator = Validator::make($request->all(), [
            'product_category_id' => 'required',
            'shop_id' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $products = Product::where('shop_id', $request->shop_id)->where('sub_category_id', $request->product_category_id)->paginate(10);
        $data['pagination'][] = apiPagination($products);
        $data['data'] = $this->productsFormat($products);
        return response(['status' => true, 'message' => "Products List.", 'products' => $data]);
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $product = Product::find($request->product_id);
        if($product && $product->delete()){
            return response(['status' => true, 'message' => 'Product Deleted.']);
        }
        return response(['status' => false, 'message' => 'Product Not Found.']);
    }

    public function search(Request $request){
        $validator = Validator::make($request->all(), [
            'search' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $search = $request->search;
        $products = auth('api')->user()->products()->where('products.name', 'Like', '%'.$search.'%')->paginate(10);

        $data['pagination'][] = apiPagination($products);
        $data['data'] = $this->productsFormat($products);
        return response(['status' => true, 'message' => "Products List.", 'products' => $data]);
    }

    private function productsFormat($products){
        foreach ($products as $key => $value) {
            $data[] = [
                'product_id' => $value->id,
                'product_name' => $value->name,
                'image' => $value->image,
                'status' => $value->active
            ];
        }
        return $data ?? NULL;
    }

    public function productCreate(Request $request){

        try {
            $validator = Validator::make($request->all(), [
                'shop_id' => 'required',
                'category_id' => 'required',
                'name' => 'required',
                'image' => 'required|image|max:1024',
                'sub_category_id' => 'required',
                'st_actual_price' => 'required',
                'st_selling_price' => 'required',
                'st_available' => 'required',
                'st_unit' => 'required_without:st_size',
                'st_variant' => 'required_without:st_size',
                'title_id' => 'required_with:top_name',
                'top_variety' => 'required_with:top_name',
                'top_available' => 'required_with:top_name',
                'top_price' => 'required_with:top_name'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response(['status' => false, 'message' => $errors]);
            }
            DB::beginTransaction();
            $sub_category_id = $request->sub_category_id;
            $prod_count = \App\Shop::find($request->shop_id)->productCategories->count();
            $shop_cat = \App\ShopSubCategory::where('shop_id', $request->shop_id)->where('sub_category_id', $sub_category_id)->first();
            if(!$shop_cat){
                \App\ShopSubCategory::create(['shop_id' => $request->shop_id, 'sub_category_id' => $sub_category_id, 'order' => $prod_count + 1]);
            }
            $product = new Product;
            $product->name = $request->name;
            $product->variety = $request->variety;
            $product->shop_id = $request->shop_id;
            $product->cuisine_id = $request->cuisine_id;
            $product->category_id = $request->category_id;
            $product->sub_category_id = $request->sub_category_id;
            $product->description = $request->description;
            if($request->hasFile('image')){
                if($request->file('image')->isValid())
                {
                    $extension = $request->image->extension();
                    $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->name)).time()."product." .$extension;
                    $request->image->move(config('constants.product_image'), $file_path);
                    $product->image = $file_path;
                }
            }
            $product->save();

            $stock = new \App\Stock;
            $stock->variant = $request->st_variant;
            $stock->unit = $request->st_unit;
            $stock->actual_price = $request->st_actual_price;
            $stock->price = $request->st_selling_price;
            $stock->available = $request->st_available;
            $stock->size = $request->st_size;
            $stock->product_id = $product->id;
            $stock->save();
            $users = \App\User::all();
            Notification::send($users, new ProductNotification($stock));
            if(isset($request->top_name)) {
                    $topp = new \App\Topping();
                    $topp->name = $request->top_name;
                    $topp->title_id = $request->title_id;
                    $topp->title_name = \App\Title::where('id', $request->title_id)->pluck('name')->first();
                    $topp->variety = $request->top_variety;
                    $topp->price = round($request->top_price, 2);
                    $topp->available = $request->top_available;
                    $topp->product_id = $product->id;
                    $topp->save();
            }

            DB::commit();
            return response(['status' => true, 'message' => 'Product Created']);
        } catch (\Throwable $th) {
            dd($th);
            return response(['status' => false, 'message' => 'Please fill the required fields!']);
        }

    }
}
