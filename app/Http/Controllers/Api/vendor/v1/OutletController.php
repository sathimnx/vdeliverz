<?php

namespace App\Http\Controllers\Api\vendor\v1;

use App\Http\Controllers\Controller;
use App\Shop;
use App\User;
use Validator, DB, Auth, Session, Mail;
use Illuminate\Http\Request;
use App\Http\Resources\OutletResource;
use App\Http\Resources\OutletShowResource;
use App\Http\Resources\OutletEditResource;
use App\Http\Resources\OutletProductResource;
use App\Http\Resources\ShopDetailResource;
use App\Mail\SendLoginCode;
use App\Mail\RegisterSuccess;
use App\Mail\AdminOutletAdded;
use App\Mail\VendorOutletAdded;
use App\Notifications\VerificationSuccess;

class OutletController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $outlets = auth('api')->user()->outlets()->paginate(10);
        $data['pagination'][] = apiPagination($outlets);
        $data['data'] = $this->shopsFormat($outlets);

        return response(['status' => true, 'message' => 'Shops List.', 'shops' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $search = $request->search;
        $shops = auth('api')->user()->outlets()->where('name', 'Like', '%'.$search.'%')->paginate(10);

        $data['pagination'][] = apiPagination($shops);
        $data['data'] = $this->shopsFormat($shops);
        return response(['status' => true, 'message' => "shops List.", 'shops' => $data]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shop_name' => 'required|unique:shops,name',
            'latitude' => 'required',
            'shop_email' => 'required',
            'shop_mobile' => 'required',
            'longitude' => 'required',
            'street' => 'required',
            'city' => 'required',
            'area' => 'required',
            'price' => 'required',
            'shop_image' => 'required|image|max:1024',
            'banner_image' => 'required|image|max:1024',
            'weekdays' => 'required',
            'opening_time' => 'required|date_format:H:i:s',
            'closing_time' => 'required|date_format:H:i:s',
            'comission' => 'required',
            'assign' => 'required',
            'min_amount' => 'required',
            'radius' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        DB::beginTransaction();
        $user = \App\User::find(auth('api')->user()->id)->shop;
        $shop = new Shop;
        $shop->name = $request->shop_name;
        $shop->email = $request->shop_email;
        $shop->mobile = $request->shop_mobile;
        $shop->user_id = auth('api')->id();
        $shop->type_id = 1;
        $shop->latitude = $request->latitude;
        $shop->longitude = $request->longitude;
        $shop->delivery_charge = $request->delivery_charge;
        $shop->price = $request->price;
        $shop->min_amount = $request->min_amount;
        $shop->radius = $request->radius;
        $shop->assign = $request->assign;
        $shop->opens_at = $request->opening_time;
        $shop->closes_at = $request->closing_time;
        $shop->weekdays = str_replace('"', '', implode(',', $request->weekdays));
        $shop->street = $request->street;
        $shop->area = $request->area;
        $shop->active = 0;
        $shop->comission = $request->comission;
        $shop->main = $user ? 0 : 1;
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
        $shop->save();
        $cuisines = $request->get('cuisines', []);
        $shop->cuisines()->sync($cuisines);
        DB::commit();
        if(!$user){
            $VappData = [
                'fcm' => $shop->user->fcm,
                'title' => config('constants.register_success.title'),
                'body' => config('constants.register_success.v_push'),
                'icon' => '',
                'type' => 1,
            ];
            sendSingleAppNotification($VappData, env('VEN_FCM'));
            $this->newShopAdded($shop->user, $shop);
        }else{
            $this->newOutletAdded($user, $shop);
        }
        return response(['status' => true, 'message' => 'Shop Created successfully.']);
    }

    private function newShopAdded($user, $shop){
        $VappData = [
            'fcm' => $user->fcm,
            'title' => 'New Shop Registered.',
            'body' => 'Welcome onboard VDeliverz. Thanks for joining us, Our team will reach you soon.',
            'icon' => '',
            'type' => 1
        ];

        sendSingleAppNotification($VappData, env('VEN_FCM'));
        $data = [
            'subject' => 'VDeliverz New Shop Registered.',
            'admin_content' => 'New Shop Registration',

            'vendor_content' => '   Welcome to be part of VDeliverz team. We are happy to see you here. You will receive your activation mail from VDeliverz Admin within 24hrs. Kindly be Patience. In case of missed communication kindly contact our Admin Support @ +91 75988 97020 or Mail us to support@vdeliverz.in',

                'vendor_name' => $shop->user->name,
                'table' => [
                    'Registered Vendor' => $shop->user->name,
                    'Vendor Mobile' => $shop->user->mobile,
                    'Vendor Email' => $shop->user->email,
                    'Shop Name' => $shop->name,
                    'Shop Street' => $shop->street,
                    'Shop Area' => $shop->area,
                    'Shop City' => $shop->city,
                    'Registered Date' => $shop->created_at->format('d-m-Y H:i')
                ]
            ];

        Mail::to(config('constants.admin_email'))->send(new AdminOutletAdded($data));
        Mail::to($user->email)->send(new VendorOutletAdded($data));
    }

    private function newOutletAdded($user, $shop){
        $data = [
            'subject' => 'VDeliverz - Vendor - Outlet Request Submission',

            'admin_content' => $shop->name.', would like to add a new Outlet to my existing shop. I need to discuss about this and activate the same. Kindly contact me and complete the process at the earliest.',

            'vendor_content' => '   We have received a request to add new outlet to your existing shop. Our team will reach your at the earliest to process your reques and soon you will receive your outlet activation mail from VDeliverz Admin. Kindly be Patience. In case of missed communication kindly contact our Admin Support @ +91 75988 97020 or Mail us to support@vdeliverz.in',

                'vendor_name' => $shop->user->name,
                'table' => [
                    'Vendor Name' => $shop->user->name,
                    'Vendor Mobile' => $shop->user->mobile,
                    'Vendor Email' => $shop->user->email,
                    'Shop Name' => $shop->name,
                    'Shop Street' => $shop->street,
                    'Shop Area' => $shop->area,
                    'Shop City' => $shop->city,
                    'Requested Date' => $shop->created_at->format('d-m-Y H:i'),
                    'Outlet Requested' => 1

                ]
            ];

        Mail::to(config('constants.admin_email'))->send(new AdminOutletAdded($data));
        Mail::to($user->email)->send(new VendorOutletAdded($data));
    }
    // private function newOutletAdded($user, $shop){
    //     $v_data = ['shop_name' => $shop->name];
    //     $a_data = [
    //         'vendor_email' => $user->email,
    //         'vendor_name' => $user->name,
    //         'vendor_mobile' => $user->mobile,
    //         'shop_name' => $shop->name,
    //         'street' => $shop->street,
    //         'area' => $shop->area,
    //         'city' => $shop->city,
    //         'date' => now()->format('d-m-y'),
    //         'time' => now()->format('H:i')
    //     ];
    //     $subject = ['shop_name' => $user->shop->name, 'outlet_name' => $shop->name];
    //     $sub = getContent($subject, config('constants.outlet_added.title'));
    //     $alerts = [[
    //         'email' => $user->email,'content' => getContent($v_data, config('constants.outlet_added.v_email')), 'name' => $user->name, 'subject' => $sub
    //     ],[
    //         'email' => config('constants.admin_email'),'content' => getContent($a_data, config('constants.outlet_added.a_email')), 'name' => 'Admin', 'subject' => $sub
    //     ]];
    //     foreach ($alerts as $key => $value) {
    //         Mail::to($value['email'])->send(new RegisterSuccess($value));
    //     }
    // }

    /**
     * Display the specified resource.
     *
     * @param  \App\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shop_id' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $shop = Shop::find($request->shop_id);

        $cuisines = \App\Cuisine::select('id as cuisine_id', 'name')->get();
        return response(['status' => true, 'message' => 'Shop Detail.', 'referral_prefix' => '#5876432', 'shop' => new ShopDetailResource($shop), 'cuisines' => $cuisines]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function shopProducts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shop_id' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $shop = Shop::where('id', $request->shop_id)->with('products')->get();
        return response(['status' => true, 'message' => 'Shop Detail.', 'referral_prefix' => '#5876432', 'shop' => $shop]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'weekdays' => 'required',
            'opening_time' => 'required|date_format:H:i:s',
            'closing_time' => 'required|date_format:H:i:s',
            'shop_id' => 'required',
            'radius' => 'required',
            'min_amount' => 'required',
            'assign' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
            DB::beginTransaction();
            $shop = Shop::find($request->shop_id);
            $shop->opens_at = $request->opening_time;
            $shop->closes_at = $request->closing_time;
            $shop->weekdays = str_replace('"', '', implode(',', $request->weekdays));
            $shop->min_amount = $request->min_amount;
            $shop->radius = $request->radius;
            $shop->description = $request->description ?? $shop->description;
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
            return response(['status' => true, 'message' => 'Shop Updated.']);
        }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function destroy(Shop $shop)
    {
        //
    }

    // Change Status
    public function change_status(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'id' => 'required',
            'status' => 'required|integer'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $status = $request->name == 'shops' ? DB::table($request->name)->where('id', $request->id)->update(['opened' => $request->status])
                                                : DB::table($request->name)->where('id', $request->id)->update(['active' => $request->status]);
        if($status){
            return response(['status' => true, 'message' => 'Status Changed.']);
        }
        return response()->json(['status' => false, 'message' => 'Invalid request']);
    }

    private function shopsFormat($shops){
        foreach ($shops as $key => $value) {
            $data[] = [
                'shop_id' => $value->id,
                'shop_name' => $value->name,
                'image' => $value->image,
                'opened' => $value->opened,
                'verified' => $value->active,
                'primary' => $value->main
            ];
        }
        return $data ?? NULL;
    }
}