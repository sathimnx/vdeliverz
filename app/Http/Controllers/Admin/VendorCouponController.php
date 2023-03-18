<?php

namespace App\Http\Controllers\Admin;

use App\Coupon;
use App\VendorCoupon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, DB, Auth;

class VendorCouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $shop_dtl = \App\Shop::where('active', 1)->where('user_id',auth()->user()->id)->get('id');
            
            if($shop_dtl->count() > 0)
            {
                $data['coupons'] = VendorCoupon::with('shop','subCategory','Category','products')->whereHas('shop', function($q) use($shop_dtl){
                            $q->where('id', '=', $shop_dtl[0]->id);})->latest()->paginate(10);
            }
            else
            {
                $data['coupons'] = VendorCoupon::latest()->with('shop','subCategory','Category','products')->paginate(10);
            }
            

            if (request()->ajax()) {
            if(isset(request()->search) && !empty(request()->search)){
                $data['coupons'] = VendorCoupon::where('coupon_code', 'Like', '%'.request()->search.'%')->orWhere('expired_on', 'Like','%'.request()->search.'%')
                ->orWhere('max_order_amount', 'Like','%'.request()->search.'%')->with('shop','subCategory','Category','products')->latest()->paginate(10);
            }

                return view('vendorcoupons.vendorcoupon_table', array('coupons' => $data['coupons']))->render();
            }
            //dd($data);
            return view('vendorcoupons.vendorcoupon', $data ?? NULL);
        } catch (\Throwable $th) {
           // return catchResponse();
          // dd($th);
           throw $th;
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
            //->where('user_id',auth()->user())
           // dd(auth()->user());
            if(auth()->user()->hasAnyRole('admin')){
                 $shops = \App\Shop::where('active', 1)->get();
            }
            else
            {
                 $shops = \App\Shop::where('active', 1)->where('user_id',auth()->user()->id)->get();
            }
            $Categories = \App\Category::where('active', 1)->get();
            $subCategories = \App\SubCategory::where('active', 1)->get();
            $products = \App\Product::where('active', 1)->get();
            return view('vendorcoupons.vendorcoupon_create')->with('subCategories', $subCategories)->with('Categories', $Categories)
            ->with('shops', $shops)->with('product', $products)->with('Type','Create');
        } catch (\Throwable $th) {
           // return catchResponse();
            throw $th;
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
            
            $validator = Validator::make($request->all(),[
                'coupon_code' => ['required'],
                'max_order_amount' => 'required',
                'coupon_date' => 'required',
               // 'coupon_time' => 'required',
                'coupon_percentage' => 'required',
                'Discount_use_amt' => 'required'           
            ]);
            if($validator->fails()){
                flash()->error($validator->messages()->first());
                return redirect()->back();
            }
            
            $word = preg_replace('/\s+/', "", strtolower($request->coupon_code));;
            $coupon_date = date('Y-m-d', strtotime($request->coupon_date));
            $coupon_time = date('H:i:s', strtotime($request->coupon_time == '' ||null ? now() :$request->coupon_time));
            $coupon_end = $coupon_date.' '. $coupon_time;
        
            $coupon = new VendorCoupon;
            $coupon->coupon_code = strtoupper($word);
            $coupon->coupon_description = $request->coupon_description;
            $coupon->coupon_percentage = $request->coupon_percentage;
            $coupon->max_order_amount = $request->max_order_amount;
            $coupon->min_order_amt = $request->min_order_amt;
            $coupon->Discount_use_amt =$request->Discount_use_amt;
            $coupon->expired_on = $coupon_end;
            
            $coupon->category_id =$request->category;
            
            //dd($request->couponShop,$request->product_category[0],$request->products);
             $ShopName = $request->couponShop[0] =='0' ? '0' :
            \App\Shop::whereIn('id',$request->couponShop)->select(DB::raw("(GROUP_CONCAT(id SEPARATOR ',')) as `shopId`"),DB::raw("(GROUP_CONCAT(name SEPARATOR ',')) as `shopname`"))->get();
           
            $CategoryName = $request->product_category[0] == '0' ? '0' :
            \App\SubCategory::whereIn('id',$request->product_category)->select(DB::raw("(GROUP_CONCAT(id SEPARATOR ',')) as `categoryId`"),DB::raw("(GROUP_CONCAT(name SEPARATOR ',')) as `categoryname`"))->get();
            $ProductName = $request->product_dtl[0] =='0' || $request->product_dtl == null ? '0' :
            \App\Product::whereIn('id',$request->product_dtl)->select(DB::raw("GROUP_CONCAT(id SEPARATOR ',') as productId"),DB::raw("GROUP_CONCAT(name SEPARATOR ',') as productname"))->get();
         
         //dd($request->product_category,$ShopName[0],$CategoryName[0],$ProductName[0]);
            $coupon->shop_id = $ShopName[0]=='0'? 0 : $ShopName[0]->shopId;
            $coupon->sub_category_id = $CategoryName[0] =='0' ? 0 : $CategoryName[0]->categoryId;
            $coupon->product_id = $ProductName[0] =='0' ? 0 : $ProductName[0]->productId;
            $coupon->shop_name = $ShopName[0] =='0' ? 'All Shops' : $ShopName[0]->shopname;
            $coupon->sub_category_name = $CategoryName[0] =='0' ? 'All Sub Category' :  $CategoryName[0]->categoryname;
            $coupon->product_name = $ProductName[0] =='0' ? 'All Products' :  $ProductName[0]->productname;
            
            if($coupon->save()){
                // $users = \App\User::role('customer')->get();
                // Notification::send($users, new CouponNotification($coupon));
             //   $alert_data = cusCouponAlert(['coupon_code' => $coupon->coupon_code]);
               // $appData = [
             //       'users' => \App\User::where('fcm', '!=', null)->get(),
             //       'title' => $alert_data['title'],
             //       'body' => $alert_data['body'],
             //      'icon' => '',
              //      'type' => 1
              //  ];
             //   sendAppNotification($appData, env('CUS_FCM'));
                flash()->success('Coupon Created Successfully !');
                return redirect()->route('vendorcoupons.index');
            }
        } catch (\Throwable $th) {
           // return catchResponse();
            dd($th);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function show(VendorCoupon $vendorcoupon)
    {
        $shops = \App\Shop::where('active', 1)->get();
            $categories =\App\Category::where('active', 1)->get();
            $subCategories = \App\SubCategory::where('active', 1)->get();
            $products = \App\Product::where('active', 1)->get();
            //dd($coupon);
            return view('vendorcoupons.vendorcoupon_edit')->with('subCategories', $subCategories)->with('shops', $shops)
            ->with('products', $products)->with('coupon',$vendorcoupon);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function edit(VendorCoupon $vendorcoupon)
    {
        try {
              $shops = \App\Shop::where('active', 1)->get();
            $products = \App\Product::where('active', 1)->get();
            $subCategories = \App\SubCategory::where('active', 1)->get();
             $Categories = \App\Category::where('active', 1)->get();
            return view('vendorcoupons.vendorcoupon_edit')->with('subCategories', $subCategories)->with('shops', $shops)->with('Categories', $Categories)
            ->with('products', $products)->with('coupon',$vendorcoupon)->with('Type','Edit');
        } catch (\Throwable $th) {
          //  return catchResponse();
           dd($th);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,VendorCoupon $vendorcoupon)
    {
        try {
            $validator = Validator::make($request->all(),[
                'coupon_code' => ['required'],
                'max_order_amount' => 'required',
                'coupon_date' => 'required',
                'coupon_time' => 'required',
                'coupon_percentage' => 'required'
            ]);
            if($validator->fails()){
                flash()->error($validator->messages()->first());
                return redirect()->back();
            }
            $word = preg_replace('/\s+/', "", strtolower($request->coupon_code));;
            $coupon_date = date('Y-m-d', strtotime($request->coupon_date));
            $coupon_time = date('H:i:s', strtotime($request->coupon_time));
            $coupon_end = $coupon_date.' '. $coupon_time;
            // dd($coupon_end, $request->coupon_date, \Carbon\Carbon::parse($request->coupon_date)->format('d/m/Y'));

            $vendorcoupon->coupon_code = strtoupper($word);
            $vendorcoupon->coupon_description = $request->coupon_description;
            $vendorcoupon->coupon_percentage = $request->coupon_percentage;
            $vendorcoupon->max_order_amount = $request->max_order_amount;
            $vendorcoupon->min_order_amt = $request->min_order_amt;
            $vendorcoupon->Discount_use_amt =$request->Discount_use_amt;
            $vendorcoupon->expired_on = $coupon_end;
            $vendorcoupon->shop_id = $request->couponShop;
            $vendorcoupon->category_id =$request->category;
            
            $ShopName = $request->couponShop[0] =='0' ? '0' :
            \App\Shop::whereIn('id',$request->couponShop)->select(DB::raw("(GROUP_CONCAT(id SEPARATOR ',')) as `shopId`"),DB::raw("(GROUP_CONCAT(name SEPARATOR ',')) as `shopname`"))->get();
        
            $CategoryName = $request->product_category[0] == '0' ? '0' :
            \App\SubCategory::whereIn('id',$request->product_category)->select(DB::raw("(GROUP_CONCAT(id SEPARATOR ',')) as `categoryId`"),DB::raw("(GROUP_CONCAT(name SEPARATOR ',')) as `categoryname`"))->get();
            $ProductName = $request->product_dtl[0] =='0' || $request->product_dtl == null ? '0' :
            \App\Product::whereIn('id',$request->product_dtl)->select(DB::raw("GROUP_CONCAT(id SEPARATOR ',') as productId"),DB::raw("GROUP_CONCAT(name SEPARATOR ',') as productname"))->get();
         
         //dd($ProductName[0]);
        // dd($request->product_category,$ShopName[0],$CategoryName[0],$ProductName[0]);
            $vendorcoupon->shop_id = $ShopName[0]=='0'? 0 : $ShopName[0]->shopId;
            $vendorcoupon->sub_category_id = $CategoryName[0] =='0' ? 0 : $CategoryName[0]->categoryId;
            $vendorcoupon->product_id = $ProductName[0] =='0' ? 0 : $ProductName[0]->productId;
            $vendorcoupon->shop_name = $ShopName[0] =='0' ? 'All Shops' : $ShopName[0]->shopname;
            $vendorcoupon->sub_category_name = $CategoryName[0] =='0' ? 'All Sub Category' :  $CategoryName[0]->categoryname;
            $vendorcoupon->product_name = $ProductName[0] =='0' ? 'All Products' :  $ProductName[0]->productname;
            
           // dd($vendorcoupon);
            if($vendorcoupon->save()){
                flash()->success('Coupon Updated Successfully !');
                return redirect()->route('vendorcoupons.index');
            }
        } catch (\Throwable $th) {
           // return catchResponse();
            dd($th);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function destroy(VendorCoupon $coupon)
    {
        $coupon->delete();
        flash()->info('Vendor Coupon Deleted');
        return back();
    }
       public $html;
   public function getProductCategory(Request $request)
    {
        $html = '';
        $coupon='';
          if($request->shop_id[0] == 0){
          
              $html .='<option value="0">All Product Category</option>';
             
              $sub_category_ids = \App\Product::where('category_id', $request->category_id)->distinct()->get()->unique('sub_category_id');
             
              foreach ($sub_category_ids as $sub_category_id) {
                $subCategories = \App\SubCategory::where('active', 1)->where('id',$sub_category_id->sub_category_id)->distinct()->get()->unique('id');
                
                foreach ($subCategories as $subCategory) {
                   $html .= '<option value="'.$subCategory->id.'">'.$subCategory->name.'</option>';
                }
              }
           }
      else
      {
            $products = \App\Product::whereIn('shop_id', $request->shop_id)->distinct()->get()->unique('sub_category_id');
           if($products->count() > 0)
           {
               $html ='<option value="0">All Product Category</option>';
            foreach ($products as $product) {
                
                $subCategories = \App\SubCategory::where('active', 1)->where('id',$product->sub_category_id)->distinct()->get()->unique('id');
               
                foreach ($subCategories as $subCategory) {
                    $html .= '<option value="'.$subCategory->id.'">'.$subCategory->name.'</option>';
                }
            }
           }
      }
          
     return response()->json(['html' => $html]);
    }
    
        
   public function getshopdtls(Request $request)
    {
        $html = '';
        $coupon='';
          if($request->category > 0){
              
            $shops = \App\Product::where('category_id', $request->category)->distinct()->get()->unique('shop_id');
            
           if($shops->count() > 0)
           {
               $html ='<option value="0">All Shops</option>';
                foreach ($shops as $shop) {
                    
                        $shopd = \App\Shop::where('id', $shop->shop_id)->distinct()->get();
                            $html .= '<option value="'.$shopd[0]->id.'">'.$shopd[0]->name.'</option>';
                
            }
           }
      }
          //dd($html);
     return response()->json(['html' => $html]);
    }
    
    
    public function getProducts(Request $request)
    {
         try {
       $html='';
       $html= $this->html;
         if($request->sub_category_id[0] == 0){
          
           $FilteredProducts = \App\Product::where('category_id',$request->category_id)->whereIn('shop_id',$request->shop_id)->distinct()->get();
                     if($html == '') { $html.='<option value="0">All Producs</option>'; }
                    foreach ($FilteredProducts as $FilteredProduct) {
                        $html .= '<option value="'.$FilteredProduct->id.'">'.$FilteredProduct->name.'</option>';
                    }
             
           }
      else
      {
           $products = \App\Product::whereIn('sub_category_id',$request->sub_category_id)->whereIn('shop_id',$request->shop_id)->distinct()->get();
           if($products->count() > 0)
           {
               $html ='<option value="0">All Producs</option>';
                  if($html == '') { $html.='<option value="0">All Producs</option>'; }
                    foreach ($products as $product) {
                            $html .= '<option value="'.$product->id.'">'.$product->name.'</option>';
                        
                    }   
           }
      }
      
     return response()->json(['html' => $html]);
    
    }
 catch (\Throwable $th) {
           // return catchResponse();
            dd($th);
        }
}

  public function deletevendorCoupons(Request $request)
   {
          
        $vendorCoupon = VendorCoupon::where('id',$request->coupon_id)->delete();
        //dd($vendorCoupon);
        return response()->json();
   }
}
