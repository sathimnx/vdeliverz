<?php

namespace App\Http\Controllers\Admin;

use App\Delivery;
use Carbon\Carbon;
use Validator, Auth, DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ManageDeliveryController extends Controller
{
    public function index(){
        try {
            // $data['order'] = \App\Order::where('delivered_by', $id)->whereIn('order_status', [4, 2])->first();
            $data['users'] = \App\User::role('delivery-boy')->with('deliveries', 'onGoing', 'roles')->paginate(40);
            // $shops = \App\Shop::where('active', 1)->get();
            $data['delivery_boy_charge'] = \App\Charge::pluck('delivery_charge')->first();
//            dd( \App\Charge::pluck('delivery_charge'));
            $data['points'] = \App\Charge::pluck('delivery_points')->first();
            // dd($data['users']);
            return view('deliveries.deliveries', $data);
        } catch (\Throwable $th) {
           return catchResponse();
            dd($th);
        }
    }

    public function store(Request $request){
        try {
            $charge = \App\Charge::first();
            $charge->delivery_charge = $request->delivery_boy_charge;
            $charge->delivery_points = $request->points;
            $charge->save();
//            dd($shops);
            return back();
        } catch (\Throwable $th) {
            return catchResponse();
            dd($th);
        }
    }

    public function show($id){
        try {

            if (request()->ajax()) {
                $data['deliveries'] = Delivery::where('user_id', $id)->paginate(25);
                $view = view('deliveries.delivery_table', array('deliveries' => $data['deliveries']))->render();
                return response()->json(['html'=>$view]);
            }else{
                $data['user'] = \App\User::find($id);
                $data['order'] = \App\Order::where('delivered_by', $id)->whereIn('order_status', [4, 2])->first();
                $data['deliveries'] = Delivery::where('user_id', $id)->paginate(25);
            }
            return view('deliveries.delivery_detail', $data);
        } catch (\Throwable $th) {
            // return catchResponse();
            dd($th);
        }
    }
}
