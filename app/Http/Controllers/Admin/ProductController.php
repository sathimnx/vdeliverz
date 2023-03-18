<?php

namespace App\Http\Controllers\Admin;

use App\Events\PostPublished;
use App\Http\Controllers\Controller;
use App\Product;
use App\Shop;
use App\Slot;
use Illuminate\Http\Request;
use DB, Validator, Auth, Session;
use App\Authorizable;
use App\Notifications\ProductNotification;
use Notification;

class ProductController extends Controller
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
            
            $data['shops'] = \App\Shop::all();

            if(auth()->user()->hasAnyRole('admin')){
                $data['products'] = Product::with('category', 'shop', 'subCategory')->latest()->paginate(10);
            }
            if(auth()->user()->hasAnyRole('vendor')){
                $data['products'] = Product::with('category', 'shop', 'subCategory')->where('shop_id', Auth::user()->shop->id)->latest()->paginate(10);
            }
            if (request()->ajax()) {
                if(isset(request()->search) && !empty(request()->search)){
                    if(auth()->user()->hasAnyRole('vendor')){
                        $data['products'] = Product::with('category', 'shop', 'subCategory')->where('shop_id', Auth::user()->shop->id)->where('name', 'Like', '%'.request()->search.'%')
                                        ->latest()->paginate(10);
                    }else{
                        $search = request()->search;
                        $data['products'] = Product::with('category', 'shop', 'subCategory')->whereHas('shop', function($q) use($search){
                                                $q->where('name', 'Like', '%'.$search.'%');
                                            })->orWhereHas('category', function($c) use($search){
                                                $c->where('name', 'Like', '%'.$search.'%');
                                            })
                                            ->orWhereHas('subCategory', function($s) use($search){
                                                $s->where('name', 'Like', '%'.$search.'%');
                                            })->orWhere('name', 'Like', '%'.request()->search.'%')
                                            ->orWhere('description', 'Like','%'.request()->search.'%')
                                            ->latest()->paginate(10);
                        //dump($data['products']);
                    }
                }
                Session::put(['prev_page_no' => $data['products']->currentPage()]);
                if($data['products'] != null) { return view('product.product_table', array('products' => $data['products']))->render(); } else { return view('product.product_table', null); }
                //return view('product.product_table', array('products' => $data['products']))->render();
            }
            if($data['products'] != null) { return view('product.products', $data); } else { return view('product.products', null); }
         //   return view('product.products', $data ?? NULL);
        } catch (\Throwable $th) {
            return catchResponse();
            dd($th);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $data['shops'] = \App\Shop::where('active', 1)->get();
            $data['titles'] = \App\Title::where('active', 1)->get();
            if(auth()->user()->hasAnyRole('vendor')){
                $data['shops'] = \App\Shop::where('user_id', Auth::user()->id)->where('active', 1)->get();
                $data['titles'] = \App\Title::where('active', 1)->get();
            }
            $data['categories'] = \App\Category::where('active', 1)->get();
            $data['subCategories'] = \App\SubCategory::where('active', 1)->get();
            $data['cuisines'] = \App\Cuisine::where('active', 1)->get();
            return view('product.product_form', $data ?? NULL);
        } catch (\Throwable $th) {
            return catchResponse();
            dd($th);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
//             dd($request);
            $validator = Validator::make($request->all(), [
                'shop_id' => 'required',
                'category_id' => 'required',
                'name' => 'required',
                'image' => 'required|image|max:1000',
                'new_sub_category' => 'required_without:sub_category_id',

            ]);
            if($validator->fails()){
                flash()->error($validator->messages()->first());
                return back()->withInput();
            }
            DB::beginTransaction();
            $sub_category_id = $request->sub_category_id;
            if(isset($request->new_sub_category) && !empty($request->new_sub_category)){
                $sub_category = \App\SubCategory::create(['name' => $request->new_sub_category]);
                $sub_category_id = $sub_category->id;
            }
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
            $product->sub_category_id = $sub_category_id;
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
            foreach ($request->stocks as $key => $value) {
                $stock = new \App\Stock;
                $stock->variant = $value['variant'];
                $stock->unit = $value['unit'] ?? NULL;
                $stock->size = $value['size'];
                $stock->actual_price = $value['actual_price'] ? round($value['actual_price'], 2) : 0 ;
                $stock->price = $value['selling_price'] ? round($value['selling_price'], 2) : 0;
                $stock->available = $value['available'] ?? 100;
                $stock->product_id = $product->id;
                $stock->save();
                // $users = \App\User::all();
                // Notification::send($users, new ProductNotification($stock));
            }
            if(isset($request->toppings)) {
                foreach ($request->toppings as $key => $value) {
                    if(!$value['name']){
                        break;
                    }
                    $stock = new \App\Topping();
                    $stock->name = $value['name'];
                    $stock->title_id = $value['title_id'];
                    $stock->title_name = \App\Title::where('id', $value['title_id'])->pluck('name')->first();
                    $stock->variety = $value['variety'];
                    $stock->price = round($value['price'], 2);
                    $stock->available = $value['available'];
                    $stock->product_id = $product->id;
                    $stock->save();
                }
            }
            DB::commit();
            // event(new PostPublished($product));
            flash()->success('Product has been created Successfully!');
            return redirect()->route('products.index');
        } catch (\Throwable $th) {
            DB::rollback();
            return catchResponse();
            dd($th);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        $data['product'] = Product::where('id', $product->id)->with('stocks')->first();
        $data['titles'] = \App\Title::where('active', 1)->get();
        $data['toppings'] = \App\Topping::where('product_id', $product->id)->get();
        $data['shops'] = \App\Shop::all();
        if(auth()->user()->hasAnyRole('vendor')){
            if(Auth::user()->id != $product->shop->user->id){
                abort(404);
            }
            $data['shops'] = \App\Shop::where('user_id', Auth::user()->id)->where('active', 1)->get();
        }
        $data['categories'] = \App\Category::where('active', 1)->get();
        $data['subCategories'] = \App\SubCategory::where('active', 1)->get();
        $data['cuisines'] = \App\Cuisine::where('active', 1)->get();
        return view('product.product_form', $data ?? NULL);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        try {
            $data['product'] = Product::where('id', $product->id)->with('stocks')->first();
            $data['titles'] = \App\Title::where('active', 1)->get();
            $data['toppings'] = \App\Topping::where('product_id', $product->id)->get();
            $data['shops'] = \App\Shop::all();
            if(auth()->user()->hasAnyRole('vendor')){
                if(Auth::user()->id != $product->shop->user->id){
                    abort(404);
                }
                $data['shops'] = \App\Shop::where('user_id', Auth::user()->id)->where('active', 1)->get();
            }
            $data['categories'] = \App\Category::where('active', 1)->get();
            $data['subCategories'] = \App\SubCategory::where('active', 1)->get();
            $data['cuisines'] = \App\Cuisine::where('active', 1)->get();
            $data['product_opencloseTime'] = DB::table('product_opencloseTime')->where('product_id', $product->id)->get();
            return view('product.product_form', $data ?? NULL);
        } catch (\Throwable $th) {
            return catchResponse();
            dd($th);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        try {
//             dd($request);
            $validator = Validator::make($request->all(), [
                'shop_id' => 'required',
                'category_id' => 'required',
                'image' => 'image|max:1000',
                'name' => 'required',
                'new_sub_category' => 'required_without:sub_category_id',
            ]);
            if($validator->fails()){
                flash()->error($validator->messages()->first());
                return back()->withInput();
            }
            DB::beginTransaction();
            $sub_category_id = $request->sub_category_id;
            if(isset($request->new_sub_category) && !empty($request->new_sub_category)){
                $sub_category = \App\SubCategory::create(['name' => $request->new_sub_category]);
                $sub_category_id = $sub_category->id;
            }
            $prod_count = Shop::find($request->shop_id)->productCategories->count();

            $shop_cat = \App\ShopSubCategory::where('shop_id', $request->shop_id)->where('sub_category_id', $sub_category_id)->first();
            if(!$shop_cat){
                \App\ShopSubCategory::create(['shop_id' => $request->shop_id, 'sub_category_id' => $sub_category_id, 'order' => $prod_count + 1]);
            }
            $product->name = $request->name;
            $product->variety = $request->variety;
            $product->shop_id = $request->shop_id;
            $product->cuisine_id = $request->cuisine_id;
            $product->category_id = $request->category_id;
            $product->sub_category_id = $sub_category_id;
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
            foreach ($request->stocks as $key => $value) {
                $stock = \App\Stock::where('id', $value['stock_id'])->first();
                $stock->variant = $value['variant'];
                $stock->unit = $value['unit'] ?? NULL;
                $stock->size = $value['size'];
                $stock->actual_price = $value['actual_price'] ? round($value['actual_price'], 2) : 0 ;
                $stock->price = $value['selling_price'] ? round($value['selling_price'], 2) : 0;
                $stock->available = $value['available'] ?? 100;
                $stock->product_id = $product->id;
                $stock->save();
                // $users = \App\User::all();
                // Notification::send($users, new ProductNotification($stock));
            }
            if(isset($request->toppings)) {
                foreach ($request->toppings as $key => $value) {
                    $stock = \App\Topping::where('id', $value['topping_id'])->first();
                    $stock->name = $value['name'];
                    $stock->title_id = $value['title_id'];
                    $stock->title_name = \App\Title::where('id', $value['title_id'])->pluck('name')->first();
                    $stock->variety = $value['variety'];
                    $stock->price = round($value['price'], 2);
                    $stock->available = $value['available'];
                    $stock->product_id = $product->id;
                    $stock->save();
                }
            }
            DB::commit();
            flash()->success('Product has been Updated Successfully!');
            $url = route('products.index').'?page='.Session::get('prev_page_no');
            return redirect($url);
        } catch (\Throwable $th) {
            DB::rollback();
           return catchResponse();
            dd($th);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();
        flash()->success('Product Deleted!');
        return redirect()->back();
    }

    public function filterProduct($shop){
        try {
            if($shop == 'all'){
                return redirect()->route('products.index');
            }
            if(auth()->user()->hasAnyRole('admin')){
                $data['shops'] = \App\Shop::all();
                $data['products'] = \App\Product::where('shop_id', $shop)->paginate(10);
            }
            if(auth()->user()->hasAnyRole('vendor')){
                $data['shop'] = auth()->user()->shop;
                $data['products'] = auth()->user()->shop->products()->paginate(2);
            }
            return view('product.products', $data ?? NULL);
        } catch (\Throwable $th) {
            dd($th);
            return catchResponse();
            //throw $th;
        }
    }
    
     public function product_TimingsAdd(Request $request){
        try {
            if($request->openingTime != '' && $request->closingTime !='')
            {
                $data = array('open_time'=>$request->openingTime,'close_time'=>$request->closingTime,'product_id'=>$request->product_id);
                DB::table('product_opencloseTime')->insert($data);
            }
        } catch (\Throwable $th) {
            dd($th);
            return catchResponse();
            //throw $th;
        }
    }
    
    public function deleteTimingProduct(Request $request){
        try {
            DB::table('product_opencloseTime')->where('id', $request->Timing_id)->delete();
            
        } catch (\Throwable $th) {
            dd($th);
            return catchResponse();
            //throw $th;
        }
    }
}
