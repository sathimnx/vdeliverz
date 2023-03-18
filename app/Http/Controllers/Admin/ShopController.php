<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Shop;
use App\User;
use Illuminate\Http\Request;
use Validator, DB, Auth, Hash, Session;
use App\Authorizable;
use \Spatie\Permission\Models\Role;
use \Spatie\Permission\Models\Permission;

class ShopController extends Controller
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
            $data['categories'] = \App\Category::all();
            if(auth()->user()->hasAnyRole('admin')){
                $data['shops'] = Shop::with('user', 'products')->orderBy('prior')->paginate(10);
            }
            if(auth()->user()->hasAnyRole('vendor')){
                $data['shops'] = Shop::with('user', 'products')->where('user_id', Auth::user()->id)->orderBy('prior')->paginate(10);
            }
            if (request()->ajax()) {
            if(isset(request()->search) && !empty(request()->search)){
                if(auth()->user()->hasAnyRole('vendor')){
                    $data['shops'] = Shop::with('user', 'products')->where('user_id', Auth::user()->shop->id)->where('name', 'Like', '%'.request()->search.'%')
                    ->orderBy('prior')->paginate(10);
                }else{
                    $data['shops'] = Shop::with('user', 'products')->where('name', 'Like', '%'.request()->search.'%')
                                    ->orWhere('area', 'Like','%'.request()->search.'%')
                                    ->orWhere('street', 'Like','%'.request()->search.'%')
                                    ->orWhereHas('user', function($query){
                                        $query->where('name', 'Like', '%'.request()->search.'%')->orWhere('mobile', 'Like', '%'.request()->search.'%');
                                    })->orderBy('prior')->paginate(10);
                }

            }
                Session::put(['prev_page_no' => $data['shops']->currentPage()]);
                return view('shop.shop_table', array('shops' => $data['shops']))->render();
            }
            return view('shop.shops', $data ?? NULL);
        } catch (\Throwable $th) {
            return catchResponse();
           dd($th);
        }
    }

    public function shopCatFilter($id){
        if($id === 'all'){
            return redirect(route('shops.index'));
        }
        $category = \App\Category::find($id);
        $shops = collect(\App\Product::where('category_id', $id)->pluck('shop_id'))->unique();
        $data['categories'] = \App\Category::all();
            if(auth()->user()->hasAnyRole('admin')){
                $data['shops'] = Shop::with('user')->whereIn('id', $shops)->orderBy('prior')->paginate(10);
            }

            if (request()->ajax()) {
            if(isset(request()->search) && !empty(request()->search)){

                    $data['shops'] = Shop::with('user')->whereIn('id', $shops)->where('name', 'Like', '%'.request()->search.'%')
                                    ->orderBy('prior')->paginate(10);

            }
                Session::put(['prev_page_no' => $data['shops']->currentPage()]);
                return view('shop.shop_table', array('shops' => $data['shops']))->render();
            }
            return view('shop.shops', $data ?? NULL);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $data['types'] = \App\Type::all();
            $data['cuisines'] = \App\Cuisine::all();
            // $data['categories'] = \App\Category::all();
            return view('shop.shop_form', $data ?? NULL);
        } catch (\Throwable $th) {
            return catchResponse();
            //throw $th;
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
            $mobile = str_replace('+91', '', $request->mobile);
            $request->merge(['mobile' => '+91'.$mobile]);
            $validator = Validator::make($request->all(), [
                'shop_name' => 'required|unique:shops,name',
                'email' => 'required|email|unique:users',
                'mobile' => 'required|unique:users',
                'latitude' => 'required',
                'longitude' => 'required',
                'username' => 'required',
                'password' => 'required',
                'confirm_password' => 'required|same:password',
                'street' => 'required',
                'city' => 'required',
                'area' => 'required',
                'price' => 'required',
                'shop_image' => 'required',
                'banner_image' => 'required',
                'weekdays' => 'required',
                'opening_time' => 'required',
                'closing_time' => 'required',
                'comission' => 'required',
            ]);
            if($validator->fails()){
                flash()->error($validator->messages()->first());
                return back()->withInput();
            }
            DB::beginTransaction();
            $user = new User;
            if($request->hasFile('profile_image')){
                if($request->file('profile_image')->isValid())
                {
                    $extension = $request->profile_image->extension();
                    $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->email)).time()."user." .$extension;
                    $request->profile_image->move(config('constants.user_profile_img'), $file_path);
                    $user->image = $file_path;
                }
            }
            $user->password =  Hash::make($request->get('password'));
            $user->name = $request->username;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->created_by = Auth::user()->id;
            $user->save();
            $user->assignRole('vendor');

            $shop = new Shop;
            $shop->name = $request->shop_name;
            $shop->email = $request->shop_email;
            $shop->mobile = '+91'.$request->shop_mobile;
            $shop->user_id = $user->id;
            $shop->type_id = $request->type_id;
            $shop->latitude = $request->latitude;
            $shop->longitude = $request->longitude;
            $shop->delivery_charge = $request->delivery_charge;
            $shop->price = $request->price;
            $shop->min_amount = $request->min_amount;
            $shop->radius = $request->radius;
            $shop->opens_at = $request->opening_time;
            $shop->closes_at = $request->closing_time;
            $shop->weekdays = implode(',', $request->weekdays);
            $shop->street = $request->street;
            $shop->area = $request->area;
            $shop->comission = $request->comission;
            $shop->city = $request->city;
            $shop->country = $request->country;
            $shop->description = $request->description;
            if($request->hasFile('shop_image')){
                if($request->file('shop_image')->isValid())
                {
                    $extension = $request->shop_image->extension();
                    $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->shop_name)).time()."shop." .$extension;

                    $request->shop_image->move(config('constants.shop_image'), $file_path);
                    $shop->image = $file_path;
                }
            }
            if($request->hasFile('banner_image')){
                if($request->file('banner_image')->isValid())
                {
                    $extension = $request->banner_image->extension();
                    $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->shop_name)).time()."banner." .$extension;
                    $request->banner_image->move(config('constants.banner_image'), $file_path);
                    $shop->banner_image = $file_path;
                }
            }
            $shop->assign = $request->assign;
            $shop->prior = Shop::max('prior') + 1;
            $shop->save();
            $cuisines = $request->get('cuisines', []);
            $shop->cuisines()->sync($cuisines);
            DB::commit();
            flash()->success('Shop has been created Successfully!');
            return redirect()->route('shops.index');
        } catch (\Throwable $th) {
            DB::rollback();

            return catchResponse();
            dd($th);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function show(Shop $shop)
    {
        $data['shop'] = $shop;
        $data['products'] = $shop->products;
        $data['monday'] = $shop->slots()->whereRaw('find_in_set("Monday", weekdays)')->get();
        $data['tuesday'] = $shop->slots()->whereRaw('find_in_set("Tuesday", weekdays)')->get();
        $data['wednesday'] = $shop->slots()->whereRaw('find_in_set("Wednesday", weekdays)')->get();
        $data['thursday'] = $shop->slots()->whereRaw('find_in_set("Thursday", weekdays)')->get();
        $data['friday'] = $shop->slots()->whereRaw('find_in_set("Friday", weekdays)')->get();
        $data['saturday'] = $shop->slots()->whereRaw('find_in_set("Saturday", weekdays)')->get();
        $data['sunday'] = $shop->slots()->whereRaw('find_in_set("Sunday", weekdays)')->get();
        if(Auth::user()->hasAnyRole('vendor')){
            if(Auth::user()->shop->id != $shop->id){
                abort(404);
            }
        }
        return view('shop.shop_detail', $data ?? null);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function edit(Shop $shop)
    {
        try {
            $data['shop'] = Shop::find($shop->id);
            $data['cuisines'] = \App\Cuisine::all();
            $data['select'] = $shop->cuisines->pluck('id')->toArray();
            return view('shop.shop_form', $data ?? NULL);
        } catch (\Throwable $th) {
            return catchResponse();
            dd($th);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Shop $shop)
    {
        try {
            $mobile = str_replace('+91', '', $request->mobile);
            $request->merge(['mobile' => '+91'.$mobile]);
            $validator = Validator::make($request->all(), [
                'shop_name' => 'required|unique:shops,name,'.$shop->id,
                'email' => 'required|email|unique:users,email,'.$shop->user->id,
                'mobile' => 'required|unique:users,mobile,'.$shop->user->id,
                'latitude' => 'required',
                'longitude' => 'required',
                'username' => 'required',
                'confirm_password' => 'required_with:password|same:password',
                'street' => 'required',
                'city' => 'required',
                'area' => 'required',
                'price' => 'required',
                'weekdays' => 'required',
                'opening_time' => 'required',
                'closing_time' => 'required',
                'comission' => 'required',
            ]);
            if($validator->fails()){
                flash()->error($validator->messages()->first());
                return back()->withInput();
            }
            DB::beginTransaction();
            $user = $shop->user;
            if($request->hasFile('profile_image')){
                if($request->file('profile_image')->isValid())
                {

                    $extension = $request->profile_image->extension();
                    $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->email)).time()."user." .$extension;
                    $request->profile_image->move(config('constants.user_profile_img'), $file_path);
                    $user->image = $file_path;
                }
            }
            if($request->password != null){
                $user->password =  Hash::make($request->get('password'));
            }
            $user->name = $request->username;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->created_by = Auth::user()->id;
            $user->save();
            $user->assignRole('vendor');

            $shop->name = $request->shop_name;
            $shop->email = $request->shop_email;
            $shop->mobile = '+91'.$request->shop_mobile;
            $shop->user_id = $user->id;
            $shop->opens_at = $request->opening_time;
            $shop->closes_at = $request->closing_time;
            $shop->weekdays = implode(',', $request->weekdays);
            $shop->type_id = $request->type_id;
            $shop->latitude = $request->latitude;
            $shop->longitude = $request->longitude;
            $shop->price = $request->price;
            $shop->min_amount = $request->min_amount;
            $shop->radius = $request->radius;
            $shop->delivery_charge = $request->delivery_charge;
            $shop->street = $request->street;
            $shop->area = $request->area;
            $shop->comission = $request->comission;
            $shop->city = $request->city;
            $shop->country = $request->country;
            $shop->description = $request->description;
            if($request->hasFile('shop_image')){
                if($request->file('shop_image')->isValid())
                {

                    $extension = $request->shop_image->extension();
                    $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->shop_name)).time()."shop." .$extension;
                    $request->shop_image->move(config('constants.shop_image'), $file_path);
                    $shop->image = $file_path;
                }
            }
            if($request->hasFile('banner_image')){
                if($request->file('banner_image')->isValid())
                {

                    $extension = $request->banner_image->extension();
                    $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->shop_name)).time()."banner." .$extension;
                    $request->banner_image->move(config('constants.banner_image'), $file_path);
                    $shop->banner_image = $file_path;
                }
            }
            $shop->assign = $request->assign;
            $shop->save();
            $cuisines = $request->get('cuisines', []);
            $shop->cuisines()->sync($cuisines);
            DB::commit();
            flash()->success('Shop has been Updated Successfully!');
            $url = route('shops.index').'?page='.Session::get('prev_page_no');
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
     * @param  \App\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function destroy(Shop $shop)
    {
        // $shop->delete();
        // $shop->products()->delete();
        // flash()->success('Shop Deleted!');
        return redirect()->back();
    }

    public function activeFilter($id){
        if($id === 'all'){
            return redirect(route('shops.index'));
        }
        $day = now()->format('l');
        $time = now()->format('H:i:s');
        $data['shops'] = \App\Shop::whereRaw("FIND_IN_SET('".$day."', weekdays)")
        ->where('opens_at', '<=', $time)
        ->where('closes_at', '>=', $time)
        ->where('opened', 1)->where('active', 1)->paginate(10);
        if (request()->ajax()) {
            if(isset(request()->search) && !empty(request()->search)){
                // if(auth()->user()->hasAnyRole('vendor')){
                //     $data['shops'] = Shop::where('id', Auth::user()->shop->id)->where('name', 'Like', '%'.request()->search.'%')
                //                     ->latest()->paginate(10);
                // }else{
                    $data['shops'] = Shop::whereRaw("FIND_IN_SET('".$day."', weekdays)")
                                    ->where('opens_at', '<=', $time)
                                    ->where('closes_at', '>=', $time)
                                    ->where('opened', 1)->where('active', 1)->where('name', 'Like', '%'.request()->search.'%')
                                   ->latest()->paginate(10);
                // }

            }
                Session::put(['prev_page_no' => $data['shops']->currentPage()]);
                return view('shop.shop_table', array('shops' => $data['shops']))->render();
            }
            return view('shop.shops', $data ?? NULL);
    }

    public function shopUpdate(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'weekdays' => 'required',
            'opening_time' => 'required',
            'closing_time' => 'required',
        ]);
        if($validator->fails()){
            flash()->error($validator->messages()->first());
            return back()->withInput();
        }
            DB::beginTransaction();
            $shop = Shop::find($id);
            $shop->opens_at = $request->opening_time;
            $shop->closes_at = $request->closing_time;
            $shop->weekdays = implode(',', $request->weekdays);
            $shop->min_amount = $request->min_amount;
            $shop->radius = $request->radius;
            $shop->description = $request->description;
            $shop->assign = $request->assign;
            if($request->hasFile('shop_image')){
                if($request->file('shop_image')->isValid())
                {

                    $extension = $request->shop_image->extension();
                    $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->shop_name)).time()."shop." .$extension;
                    $request->shop_image->move(config('constants.shop_image'), $file_path);
                    $shop->image = $file_path;
                }
            }
            if($request->hasFile('banner_image')){
                if($request->file('banner_image')->isValid())
                {

                    $extension = $request->banner_image->extension();
                    $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->shop_name)).time()."banner." .$extension;
                    $request->banner_image->move(config('constants.banner_image'), $file_path);
                    $shop->banner_image = $file_path;
                }
            }
            $shop->save();
            $cuisines = $request->get('cuisines', []);
            $shop->cuisines()->sync($cuisines);
            DB::commit();
            flash()->success('Shop Updated Successfully!');
            // $url = route('shops.index').'?page='.Session::get('prev_page_no');
            return redirect(route('shops.index'));
    }
    public function priorChange(Request $request){
        $shop = Shop::find($request->id);
        $old_order = $shop->prior;
        $shop->update(['prior' => null]);
        $old_service = Shop::where('prior', $request->num)->first();
        Shop::where('prior', $request->num)->update(['prior' => $old_order]);
        $shop->update(['prior' => $request->num]);
        return response(['status' => true, 'id' => $old_service->id ?? null, 'prior' => $old_order]);
    }
}