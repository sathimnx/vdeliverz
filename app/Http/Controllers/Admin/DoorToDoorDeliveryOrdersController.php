<?php

namespace App\Http\Controllers\Admin;

use App\Coupon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, DB, Auth;

class DoorToDoorDeliveryOrdersController extends Controller
{
    public function index()
    {
        try {
            $data['orders'] = DB::table('door_to_doorDelivery')
             ->join('dTd_pickup_address_dtls', 'dTd_pickup_address_dtls.id', '=', 'door_to_doorDelivery.pickup_addressId')
             ->join('dTd_drop_address_dtls', 'dTd_drop_address_dtls.id', '=', 'door_to_doorDelivery.droupup_addressId')
             ->join('dTd_pickup_dtls', 'dTd_pickup_dtls.id', '=', 'door_to_doorDelivery.pickup_itemId')
             ->leftjoin('dTd_order_status', 'door_to_doorDelivery.order_status', '=', 'dTd_order_status.id')
             ->leftjoin('users', 'users.id', '=', 'door_to_doorDelivery.delivery_boy')
             ->select('order_id','pickup_date','pickup_time','pickup_mobile','dTd_pickup_address_dtls.address as pickup_address',
                'dTd_pickup_dtls.item_name','dTd_pickup_dtls.item_detail','droupup_mobile','dTd_drop_address_dtls.address as drop_address','grand_total as total',
                //DB::raw('(case when(order_status == 1) then Order Placed else '' end) AS order_status')
                'dTd_order_status.order_status AS order_status','delivery_boy','dTd_drop_address_dtls.latitude','dTd_drop_address_dtls.longitude','users.name as delivery_boyDtl'
                )->orderby('door_to_doorDelivery.order_status','desc')->orderby('delivery_boy','asc')->orderby('order_id','asc')
             ->paginate(15);
             
             $data['charges'] = DB::table('dTd_charges')->get();
             
             $data['vehicleType'] = DB::table('dTd_transportation')->get();
             
            return view('doortodoor_delivery_order.deliveries', $data ?? NULL);
        } catch (\Throwable $th) {
            return catchResponse();
            //throw $th;
        }
    }
    
    public function getdelivery_boyBy_type(Request $request)
    {
        try {
            
            //dd($request->vehicle_type);
            $users_dtl  =  \App\User::role('delivery-boy')->whereRaw('FIND_IN_SET(?, driving_preference)', [$request->vehicle_type])->distinct()->get();
            //dd($users_dtl);
            $html = '';
            if($users_dtl->count() > 0)
            {
                $html .=  '<option value="0">Select Delivery Boy</option>';
                foreach ($users_dtl as $user) {
                        $html .=  '<option value="'.$user->id.'">'.$user->name.'('.$user->driving_preference .')' .'</option>';
                    }
            }
             return response()->json(['html' => $html]);
                    
        } catch (\Throwable $th) {
            //return catchResponse();
            throw $th;
        }
    }
    
    public function assign_deliveryBoy(Request $request)
    {
        try {
            $data = DB::table('door_to_doorDelivery')->where('order_id',$request->order_id)->update([
        'order_status' => $request->order_stat
    ]);
           //dd($data);
             return response($data);
            
        } catch (\Throwable $th) {
            //return catchResponse();
            throw $th;
        }
    }
    
     public function get_TwoWheelerCharges(Request $request)
     {
          try {
             $dTd_charges = DB::table('dTd_charges')->where('vehicle_type','Two Wheeler')->select('basic_charge','basic_km','extra_charge')->first();
             
             return response(['basic_charge'=>$dTd_charges->basic_charge,'basic_km'=>$dTd_charges->basic_km,'extra_charge'=>$dTd_charges->extra_charge]);
          } catch (\Throwable $th) {
            //return catchResponse();
            throw $th;
        }
    }
     public function save_newVehicle(Request $request)
     {
          try {
              $data=array('vehicle_type'=>$request->vehicle_type,'vehicle_type_id'=>$request->vehicle_type_id,"vehicle_name"=>$request->vehicle_name,"vehicle_number"=>$request->vehicle_number,"basic_charge"=>$request->dTd_basicCharge,
                "basic_km"=>$request->dTd_basic_KM,"extra_charge"=>$request->dTd_extraCharge);
     
    
              DB::table('dTd_charges')->insert($data);
             
               return response($data);
          } catch (\Throwable $th) {
            //return catchResponse();
            throw $th;
        }
    }
    
    public function getOrders_byType(Request $request)
     {
          try {
             $charges = DB::table('dTd_charges')->where('vehicle_type',$request->vehicle_type)->get();
             
            return response($charges);
              
          } catch (\Throwable $th) {
            //return catchResponse();
            throw $th;
        }
    }
    
     public function charge_filter($type){
        try {
             $data['orders'] = DB::table('door_to_doorDelivery')
             ->join('dTd_pickup_address_dtls', 'dTd_pickup_address_dtls.id', '=', 'door_to_doorDelivery.pickup_addressId')
             ->join('dTd_drop_address_dtls', 'dTd_drop_address_dtls.id', '=', 'door_to_doorDelivery.droupup_addressId')
             ->join('dTd_pickup_dtls', 'dTd_pickup_dtls.id', '=', 'door_to_doorDelivery.pickup_itemId')
             ->leftjoin('users', 'users.id', '=', 'door_to_doorDelivery.delivery_boy')
             ->select('order_id','pickup_date','pickup_time','pickup_mobile','dTd_pickup_address_dtls.address as pickup_address',
                'dTd_pickup_dtls.item_name','dTd_pickup_dtls.item_detail','droupup_mobile','dTd_drop_address_dtls.address as drop_address','grand_total as total',
                //DB::raw('(case when(order_status == 1) then Order Placed else '' end) AS order_status')
                'door_to_doorDelivery.order_status AS order_status','delivery_boy','dTd_drop_address_dtls.latitude','dTd_drop_address_dtls.longitude','users.name as delivery_boyDtl'
                )->orderby('door_to_doorDelivery.order_status','desc')->orderby('delivery_boy','asc')->orderby('order_id','asc')
             ->paginate(15);
             
            $data['vehicleType'] = DB::table('dTd_transportation')->get();
             
             if($type > 0)
             {
                $transporattion_name = DB::table('dTd_transportation')->where('id',$type)->select('transportation_name')->first();
               
                $data['charges'] = DB::table('dTd_charges')->where('vehicle_type',$transporattion_name->transportation_name)->get();
             }
             else
             {
                $data['charges'] = DB::table('dTd_charges')->get(); 
             }
       
           // dd($data);
            return view('doortodoor_delivery.deliveries', $data ?? NULL);
        
        } catch (\Throwable $th) {
            return catchResponse();
        }
    }
    
      public function edit(Request $request)
     {
          try {
                $charges = DB::table('dTd_charges')->where('id',$request->charge_id)
                ->first();
         
                return response(['vehicle_name'=>$charges->vehicle_name,'vehicle_number'=>$charges->vehicle_number,'vehicle_type_id'=>$charges->vehicle_type_id,'basic_charge'=>$charges->basic_charge,'basic_km'=>$charges->basic_km,'extra_charge'=>$charges->extra_charge]);
              
          } catch (\Throwable $th) {
            //return catchResponse();
            throw $th;
        }
    }
    
     public function getDeliveryboy_pendingOrders(Request $request)
     {
          try {
                
                $orders = DB::table('door_to_doorDelivery')
                     ->join('dTd_pickup_address_dtls', 'dTd_pickup_address_dtls.id', '=', 'door_to_doorDelivery.pickup_addressId')
                     ->join('dTd_drop_address_dtls', 'dTd_drop_address_dtls.id', '=', 'door_to_doorDelivery.droupup_addressId')
                     ->join('dTd_pickup_dtls', 'dTd_pickup_dtls.id', '=', 'door_to_doorDelivery.pickup_itemId')
                     ->leftjoin('users', 'users.id', '=', 'door_to_doorDelivery.delivery_boy')
                     ->where('delivery_boy',$request->delivery_boyId)
                     ->select('order_id','pickup_date','pickup_time','dTd_pickup_address_dtls.address as pickup_address',
                        'dTd_pickup_dtls.item_name','dTd_pickup_dtls.item_detail','dTd_drop_address_dtls.address as drop_address'
                        )->orderby('door_to_doorDelivery.order_status','desc')->orderby('delivery_boy','asc')->orderby('order_id','asc')
                     ->paginate(3);
                     
         
                return response($orders);
              
          } catch (\Throwable $th) {
            //return catchResponse();
            throw $th;
        }
    }
    
}