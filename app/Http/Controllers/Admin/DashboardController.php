<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB, Validator, Auth, Mail;
use App\Mail\AdminRegisterSuccess;
use App\Mail\VendorRegisterSuccess;

class DashboardController extends Controller
{
    public function index(){
        if(auth()->user()->hasAnyRole('provider')){
            return redirect(route('demand.dashboard.index'));
        }
        $today = today();
        $year = date("Y");
        $month = date("m");
        $data['dates'] = [];
        $data['daily'] = [];
        for($i=1; $i < $today->daysInMonth + 1; ++$i) {
            $day = \Carbon\Carbon::createFromDate($today->year, $today->month, $i)->format('Y-m-d');
            $data['dates'][] = $i;
            if(Auth::user()->hasAnyRole('vendor')){
                $shop_id = Auth::user()->shop->id;
                $query = \App\Order::where('shop_id', $shop_id)
                ->where('delivered_at', '!=', null)->whereYear('delivered_at', '=', $year)
                ->whereMonth('delivered_at', '=', $month)
                ->whereDate('delivered_at', '=', $day);
                $data['daily'][] =  round( $query->sum(DB::raw('amount')), 0);
            }else{
                $query = \App\Order::
                where('delivered_at', '!=', null)->whereYear('delivered_at', '=', $year)
                ->whereMonth('delivered_at', '=', $month)
                ->whereDate('delivered_at', '=', $day);
                $data['daily'][] =  round( $query->sum(DB::raw('amount')), 0);
            }
            $data['total_orders'][] = $query->count();

        }
        // dd($data);
        $data['show_dates'] = implode(', ', $data['dates']);
        if(Auth::user()->hasAnyRole('admin')){
            $data['shops'] = \App\Shop::all();
            $data['orders'] = \App\Order::where('confirmed_at', '!=', null)->get();
            $data['monthly'] = $monthly = \App\Order::where('delivered_at', '!=', null)->whereYear('delivered_at', '=', $year)
                                ->select(DB::raw('SUM(amount) as amount, MONTH( delivered_at ) as month'))
                                ->groupBy(DB::raw('MONTH(delivered_at) ASC'))->get();
        }
        if(auth()->user()->hasAnyRole('vendor')){
            $data['orders'] = \App\Order::where('confirmed_at', '!=', null)->whereHas('cart', function($query){
                $query->where('shop_id', Auth::user()->shop->id);
            })->get();
            $data['monthly'] = $monthly = \App\Order::where('delivered_at', '!=', null)
                                ->whereHas('cart', function($query){
                                    $query->where('shop_id', Auth::user()->shop->id);
                                })
                                ->select(DB::raw('SUM(amount) as amount, MONTH( delivered_at ) as month'))
                                ->groupBy(DB::raw('MONTH(delivered_at) ASC'))->get();
        }
        $data['unassigned'] = $data['orders']->where('order_status', 1)->count();
        $data['orders_count'] = $data['orders']->where('order_status', 3)->count();
        $data['earnings'] = $data['orders']->where('paid', 1)->where('delivered_at', '!=', null)->sum('amount');
        $data['users'] = \App\User::where('active', 1)->count();

        $data['monthly_data'] = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0];
        $replace = [];
        for ($i=0; $i < 12 ; $i++) {
            foreach ($monthly as $key => $value) {
                if(isset($value->month) && $value->month == $i + 1){
                    array_push($replace, round($value->amount, 2));
                    unset($monthly[$key]);
                    break;
                }else{
                    array_push($replace, 0);
                    break;
                }
            }
        }
        $data['final'] = array_replace($data['monthly_data'], $replace);
        // dd($data);
        return view('dashboard.dashboard', $data ?? null);
    }

    public function dashboardFilter($shop, $year, $month){

        if((Auth::user()->hasAnyRole('vendor') && auth()->user()->shop->id != $shop) || ($shop === 'all' && $year == null)){
            return redirect()->route('dashboard.index');
        }
        $today = $year.'-'.$month.'-01';
        $today = Carbon::parse($today);
        if($today > today()){
            return back();
        }
        $data['dates'] = [];
        $data['daily'] = [];
        for($i=1; $i < $today->daysInMonth + 1; ++$i) {
            $day = \Carbon\Carbon::createFromDate($today->year, $today->month, $i)->format('Y-m-d');
            $data['dates'][] = $i;
                $data['daily'][] =  $shop === 'all' ?
                    round(\App\Order::
                   where('delivered_at', '!=', null)->whereYear('delivered_at', '=', $year)
                    ->whereMonth('delivered_at', '=', $month)
                    ->whereDate('delivered_at', '=', $day)
                    ->sum(DB::raw('amount')), 0) :
                    round(\App\Order::where('shop_id', $shop)->where('delivered_at', '!=', null)->whereYear('delivered_at', '=', $year)
                    ->whereMonth('delivered_at', '=', $month)
                    ->whereDate('delivered_at', '=', $day)
                    ->sum(DB::raw('amount')), 0);
        }

        $data['show_dates'] = implode(', ', $data['dates']);
        $data['shops'] = \App\Shop::all();
        $data['orders'] = $shop === 'all' ? \App\Order::where('confirmed_at', '!=', null)->get() : \App\Order::where('confirmed_at', '!=', null)->where('shop_id', $shop)->get();
        $data['monthly'] = $monthly = $shop === 'all' ? \App\Order::where('delivered_at', '!=', null)->whereYear('delivered_at', '=', $year)
            ->select(DB::raw('SUM(amount) as amount, MONTH( delivered_at ) as month'))
            ->groupBy(DB::raw('MONTH(delivered_at) ASC'))->get() :
            \App\Order::where('delivered_at', '!=', null)->where('shop_id', $shop)->whereYear('delivered_at', '=', $year)
            ->select(DB::raw('SUM(amount) as amount, MONTH( delivered_at ) as month'))
            ->groupBy(DB::raw('MONTH(delivered_at) ASC'))->get();

        $data['unassigned'] = $data['orders']->where('order_status', 1)->count();
        $data['orders_count'] = $data['orders']->where('order_status', 3)->count();
        $data['earnings'] = $data['orders']->where('delivered_at', '!=', null)->sum('amount');
        $data['users'] = \App\User::where('active', 1)->count();

        $data['monthly_data'] = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0];
        $replace = [];
        for ($i=0; $i < 12 ; $i++) {
            foreach ($monthly as $key => $value) {
                if(isset($value->month) && $value->month == $i + 1){
                    array_push($replace, round($value->amount, 2));
                    unset($monthly[$key]);
                    break;
                }else{
                    array_push($replace, 0);
                    break;
                }
            }
        }
        $data['final'] = array_replace($data['monthly_data'], $replace);
      //  dd($data);
        return view('dashboard.dashboard', $data ?? null);
    }

    // Change Status
    public function change_status(Request $request){

    try {
        $customer =  DB::table($request->name)->where('id', $request->id)->update([$request->column => $request->status]);
        if($request->name === 'shops' && $request->status){
            $shop = \App\Shop::where('id', $request->id)->first();
            $arr = ['shop_name' => $shop->name];
            $content = getContent($arr, config('constants.shop_approved.v_push'));
            $appData = [
                'fcm' => $shop->user->fcm,
                // 'users' => $shop->user,
                'title' => config('constants.shop_approved.title'),
                'body' => $content,
                'icon' => '',
                'type' => 1,
            ];
            sendSingleAppNotification($appData, env('VEN_FCM'));
        }
        if($request->name === 'users' && $request->status){
            $user = \App\User::role('vendor')->where('id', $request->id)->first();
            if($user){
                $VappData = [
                    'fcm' => $user->fcm,
                    'title' => 'Account Verified Successfully',
                    'body' => 'Hurray!, You became an Verified Partner, Start adding your Products and increase your sales online through  VDeliverz Platform',
                    'icon' => '',
                    'type' => 1
                ];

                sendSingleAppNotification($VappData, env('VEN_FCM'));
                $data = [
                    'subject' => 'VDeliverz - Vendor Account Activated Successfully',
                    'admin_content' => 'Vendor Details, ',

                    'vendor_content' => '   We are happy to announce that your account has been verified and Activated by VDeliverz Admin. Get ready and start adding your shop information, outlets Products and other details to display your shop in VDeliverz Customer App to enhance your sales and marketing. VDeliverz vendor login link - '.env('APP_URL').' For any queries or support kindly reach our admin support centre @ +91 75988 97020 or Mail us to hello@VDeliverzdelivery.com',

                        'vendor_name' => $user->name,
                        'table' => [
                            'Registered Vendor' => $user->name,
                            'Vendor Mobile' => $user->mobile,
                            'Vendor Email' => $user->email,
                            'Registered Date' => $user->created_at->format('d-m-Y H:i'),
                            'Verified Date' => now()->format('d-m-Y H:i')
                        ]
                    ];

                Mail::to(config('constants.admin_email'))->send(new AdminRegisterSuccess($data));
                Mail::to($user->email)->send(new VendorRegisterSuccess($data));
            }
        }
        if($customer){
            return response()->json(['status' => 1, 'message' => 'Success']);
        }
        return response()->json(['status' => 0, 'message' => 'Please Refersh and Try Again!']);
        } catch (\Throwable $th) {
        // dd($th);
        return catchResponse();
        }
    }

    public function checkUnique(Request $request){
        $customer =  DB::table($request->table)->where($request->field, $request->value)->first();
        if($customer && $request->value != null){
            return response()->json(['status' => false, 'message' => ucfirst($request->field).' already taken!']);
        }
        return response()->json(['status' => true]);
    }

    public function notifications(Request $request){
        $view = view('layouts.notifications')->render();
        $order = \App\Order::find($request->id);
        if(Auth::user()->hasAnyRole('admin') || Auth::user()->shop->id == $order->shop->id){
            return response(['status' => true, 'message' => 'notifications', 'prefix' => $order->search ?? null, 'notify_view' => $view]);
        }
        return response(['status' => false, 'message' => 'notifications', 'notify_view' => $view]);
    }

    public function notificationsReadAll(){
        auth()->user()->unreadNotifications()->where('type', 'App\Notifications\AdminOrder')->get()->markAsRead();
        $view = view('layouts.notifications')->render();
        return response(['status' => true, 'message' => 'Notifications marked as read.', 'notify_view' => $view]);
    }

}