<?php

namespace App\Http\Controllers\Demand;

use App\Provider;
use App\Http\Controllers\Controller;
use App\Shop;
use App\User;
use Illuminate\Http\Request;
use Validator, DB, Auth, Hash, Session;
use App\DemandAuthorizable;
use \Spatie\Permission\Models\Role;
use \Spatie\Permission\Models\Permission;

class ProviderController extends Controller
{
    use DemandAuthorizable;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            if(auth()->user()->hasAnyRole('admin')){
                $data['providers'] = Provider::latest()->paginate(10);
            }
            if(auth()->user()->hasAnyRole('provider')){
                $data['providers'] = Provider::where('user_id', Auth::user()->id)->latest()->paginate(10);
            }
            if (request()->ajax()) {
            if(isset(request()->search) && !empty(request()->search)){
                if(auth()->user()->hasAnyRole('provider')){
                    $data['providers'] = Provider::where('id', Auth::user()->provider->id)->where('name', 'Like', '%'.request()->search.'%')
                                    ->latest()->paginate(10);
                }else{
                    $data['providers'] = Provider::where('name', 'Like', '%'.request()->search.'%')
                                    ->orWhere('area', 'Like','%'.request()->search.'%')
                                    ->orWhere('street', 'Like','%'.request()->search.'%')
                                    ->orWhereHas('user', function($query){
                                        $query->where('name', 'Like', '%'.request()->search.'%')->orWhere('mobile', 'Like', '%'.request()->search.'%');
                                    })->latest()->paginate(10);
                }

            }
                Session::put(['prev_page_no' => $data['providers']->currentPage()]);
                return view('demand.provider.provider_table', array('providers' => $data['providers']))->render();
            }
            return view('demand.provider.providers', $data ?? NULL);
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
            $data['types'] = \App\Type::all();
            $data['services'] = \App\Service::where('order', '!=', 1)->get();
            // $data['categories'] = \App\Category::all();
            return view('demand.provider.provider_form', $data ?? NULL);
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
            // dd($request);
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
                'weekdays' => 'required',
                'opening_time' => 'required',
                'closing_time' => 'required',
                'comission' => 'required',
                'delivery_charge' => 'required'
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
            $user->assignRole('provider');

            $provider = new Provider;
            $provider->name = $request->shop_name;
            $provider->email = $request->shop_email;
            $provider->user_id = $user->id;
            $provider->type_id = $request->type_id;
            $provider->latitude = $request->latitude;
            $provider->longitude = $request->longitude;
            $provider->delivery_charge = $request->delivery_charge;
            // $provider->hour_price = $request->hour_price;
            // $provider->job_price = $request->job_price;
            // $provider->min_amount = $request->min_amount;
            $provider->radius = $request->radius;
            $provider->opens_at = $request->opening_time;
            // 11.389068609514204, 79.6794523106512
            $provider->closes_at = $request->closing_time;
            $provider->weekdays = implode(',', $request->weekdays);
            $provider->street = $request->street;
            $provider->area = $request->area;
            $provider->comission = $request->comission;
            $provider->city = $request->city;
            $provider->country = $request->country;
            $provider->description = $request->description;
            if($request->hasFile('shop_image')){
                if($request->file('shop_image')->isValid())
                {
                    $extension = $request->shop_image->extension();
                    $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->shop_name)).time()."provider." .$extension;

                    $request->shop_image->move(config('constants.demand_shop_image'), $file_path);
                    $provider->image = $file_path;
                }
            }
            if($request->hasFile('banner_image')){
                if($request->file('banner_image')->isValid())
                {
                    $extension = $request->banner_image->extension();
                    $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->shop_name)).time()."banner." .$extension;
                    $request->banner_image->move(config('constants.demand_banner_image'), $file_path);
                    $provider->banner_image = $file_path;
                }
            }
            $provider->assign = 1;
            $provider->save();
            if(isset($request->services)){
                foreach ($request->services as $key => $value) {
                    $service = new \App\ProviderService;
                    $service->provider_id = $provider->id;
                    $service->sub_service_id = $value['sub_service_id'];
                    $service->hour = $value['hour_price'];
                    $service->job = $value['job_price'];
                    $service->save();
                }
            }
            // $services = $request->get('services', []);
            // $provider->subServices()->sync($services);
            DB::commit();
            flash()->success('Shop has been created Successfully!');
            return redirect()->route('demand.providers.index');
        } catch (\Throwable $th) {
            DB::rollback();
            // return catchResponse();
            dd($th);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function show(Provider $provider)
    {
        $data['provider'] = $provider;
        $data['products'] = $provider->products;
        $data['monday'] = $provider->slots()->whereRaw('find_in_set("Monday", weekdays)')->get();
        $data['tuesday'] = $provider->slots()->whereRaw('find_in_set("Tuesday", weekdays)')->get();
        $data['wednesday'] = $provider->slots()->whereRaw('find_in_set("Wednesday", weekdays)')->get();
        $data['thursday'] = $provider->slots()->whereRaw('find_in_set("Thursday", weekdays)')->get();
        $data['friday'] = $provider->slots()->whereRaw('find_in_set("Friday", weekdays)')->get();
        $data['saturday'] = $provider->slots()->whereRaw('find_in_set("Saturday", weekdays)')->get();
        $data['sunday'] = $provider->slots()->whereRaw('find_in_set("Sunday", weekdays)')->get();
        if(Auth::user()->hasAnyRole('vendor')){
            if(Auth::user()->provider->id != $provider->id){
                abort(404);
            }
        }
        return view('demand.provider.provider_detail', $data ?? null);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function edit(Provider $provider)
    {
        try {
            $data['provider'] = Provider::find($provider->id);
            $data['services'] = \App\Service::where('order', '!=', 1)->with('subServices')->get();
            $data['selected'] = $provider->subServices;
            // dd($data['selected']);
            return view('demand.provider.provider_form', $data ?? NULL);
        } catch (\Throwable $th) {
            // return catchResponse();
            dd($th);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Provider $provider)
    {
        try {
            // dd($request);
            $validator = Validator::make($request->all(), [
                'shop_name' => 'required|unique:providers,name,'.$provider->id,
                'email' => 'required|email|unique:users,email,'.$provider->user->id,
                'mobile' => 'required|unique:users,mobile,'.$provider->user->id,
                'latitude' => 'required',
                'longitude' => 'required',
                'username' => 'required',
                'confirm_password' => 'required_with:password|same:password',
                'street' => 'required',
                'city' => 'required',
                'area' => 'required',
                'weekdays' => 'required',
                'opening_time' => 'required',
                'closing_time' => 'required',
                'comission' => 'required',
                'delivery_charge' => 'required'
            ]);
            if($validator->fails()){
                flash()->error($validator->messages()->first());
                return back()->withInput();
            }
            DB::beginTransaction();
            $user = $provider->user;
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
            $user->assignRole('provider');

            $provider->name = $request->shop_name;
            $provider->email = $request->shop_email;
            $provider->user_id = $user->id;
            $provider->opens_at = $request->opening_time;
            $provider->closes_at = $request->closing_time;
            $provider->weekdays = implode(',', $request->weekdays);
            $provider->type_id = $request->type_id;
            $provider->latitude = $request->latitude;
            $provider->longitude = $request->longitude;
            // $provider->hour_price = $request->hour_price;
            // $provider->job_price = $request->job_price;
            // $provider->min_amount = $request->min_amount;
            $provider->radius = $request->radius;
            $provider->delivery_charge = $request->delivery_charge;
            $provider->street = $request->street;
            $provider->area = $request->area;
            $provider->comission = $request->comission;
            $provider->city = $request->city;
            $provider->country = $request->country;
            $provider->description = $request->description;
            if($request->hasFile('shop_image')){
                if($request->file('shop_image')->isValid())
                {

                    $extension = $request->shop_image->extension();
                    $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->shop_name)).time()."provider." .$extension;
                    $request->shop_image->move(config('constants.demand_shop_image'), $file_path);
                    $provider->image = $file_path;
                }
            }
            if($request->hasFile('banner_image')){
                if($request->file('banner_image')->isValid())
                {

                    $extension = $request->banner_image->extension();
                    $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->shop_name)).time()."banner." .$extension;
                    $request->banner_image->move(config('constants.demand_banner_image'), $file_path);
                    $provider->banner_image = $file_path;
                }
            }
            // $provider->assign = $request->assign;
            $provider->save();
            if(isset($request->services)){
                foreach ($request->services as $key => $value) {
                    $service = \App\ProviderService::where('id', $value['provider_sub_id'])->first();
                    $service->provider_id = $provider->id;
                    $service->sub_service_id = $value['sub_service_id'];
                    $service->hour = $value['hour_price'];
                    $service->job = $value['job_price'];
                    $service->save();
                }
            }
            // $services = $request->get('services', []);
            // $provider->subServices()->sync($services);
            DB::commit();
            flash()->success('Shop has been Updated Successfully!');
            $url = route('demand.providers.index').'?page='.Session::get('prev_page_no');
            // return redirect($url);
            return back();
        } catch (\Throwable $th) {
            DB::rollback();
            //  return catchResponse();
            dd($th);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function destroy(Provider $provider)
    {
        // $provider->delete();
        // $provider->products()->delete();
        // flash()->success('Shop Deleted!');
        return redirect()->back();
    }

    public function addSubServices(Request $request){
        foreach ($request->services as $key => $value) {
            $service = new \App\ProviderService;
            $service->provider_id = $request->provider_id;
            $service->sub_service_id = $value['sub_service_id'];
            $service->hour = $value['hour_price'];
            $service->job = $value['job_price'];
            $service->save();
        }
        flash()->success('Service added.');
        return back();
    }
    public function deleteSubServices(Request $request, $id){

            $service = \App\ProviderService::find($id)->delete();
            flash()->info('Service Deleted.');
            return back();

    }

    public function providerUpdate(Request $request, $id){
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
            $provider = Provider::find($id);
            $provider->opens_at = $request->opening_time;
            $provider->closes_at = $request->closing_time;
            $provider->weekdays = implode(',', $request->weekdays);
            $provider->min_amount = $request->min_amount;
            $provider->radius = $request->radius;
            $provider->description = $request->description;
            // $provider->assign = $request->assign;
            if($request->hasFile('shop_image')){
                if($request->file('shop_image')->isValid())
                {

                    $extension = $request->shop_image->extension();
                    $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->shop_name)).time()."provider." .$extension;
                    $request->shop_image->move(config('constants.demand_shop_image'), $file_path);
                    $provider->image = $file_path;
                }
            }
            if($request->hasFile('banner_image')){
                if($request->file('banner_image')->isValid())
                {

                    $extension = $request->banner_image->extension();
                    $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->shop_name)).time()."banner." .$extension;
                    $request->banner_image->move(config('constants.demand_banner_image'), $file_path);
                    $provider->banner_image = $file_path;
                }
            }
            $provider->save();
            if(isset($request->services) && $provider->c_ser == 1){
                foreach ($request->services as $key => $value) {
                    $service = \App\ProviderService::where('id', $value['provider_sub_id'])->first();
                    $service->provider_id = $provider->id;
                    $service->sub_service_id = $value['sub_service_id'];
                    $service->hour = $value['hour_price'];
                    $service->job = $value['job_price'];
                    $service->save();
                }
            }
            DB::commit();
            flash()->success('Provider Updated Successfully!');
            // $url = route('shops.index').'?page='.Session::get('prev_page_no');
            return redirect(route('demand.providers.index'));
    }
}