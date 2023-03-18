<?php

namespace App\Http\Controllers\Admin;

use App\Coupon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, DB, Auth;

class DoorToDoorDeliveryController extends Controller
{
    public function index()
    {
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
             
             $data['charges'] = DB::table('dTd_charges')->get();
             
             $data['vehicleType'] = DB::table('dTd_transportation')->get();
             
            return view('doortodoor_delivery.deliveries', $data ?? NULL);
        } catch (\Throwable $th) {
            return catchResponse();
            //throw $th;
        }
    }
    
    public function get_nearest_deliveryBoy(Request $request)
    {
        try {
            $lat = $request->db_latitude;
            $lon = $request->db_longitude;
            
            $html = '';   
            //$users = DB::table('addresses')
            //    ->select(
            //    DB::raw("user_id,( 3959 * acos( cos( radians('$lat') ) * cos( radians( latitude ) ) * cos( radians( longitude ) -
            //    radians('$lon') ) + sin( radians('$lat') ) * sin( radians( latitude ) ) ) ) AS distance"));
                
            //$users  = $users->having('distance', '<', 50);
            //$users  =  $users->orderBy('distance', 'asc');
            //->whereIn('id',$users->pluck('user_id'))
            $users_dtl  =  \App\User::role('delivery-boy')->select('id','name','driving_preference')->orderby('name','asc')->get();
           // dd($users_dtl);
            foreach ($users_dtl as $user) {
                    $html .= $user->driving_preference !=  null ? '<option value="'.$user->id.'">'.$user->name.'('.$user->driving_preference .')' .'</option>' :
                        '<option value="'.$user->id.'">'.$user->name .'</option>';
                }
               // dd($html);
             return response()->json(['html' => $html]);
                    
        } catch (\Throwable $th) {
            //return catchResponse();
            throw $th;
        }
    }
    
    public function assign_deliveryBoy(Request $request)
    {
        try {
            $data = DB::table('door_to_doorDelivery')->where('order_id',$request->order_id)->update(array('delivery_boy' => $request->delivery_boyId,'order_status'=>'2')); 
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
              if($request->type == 'add')
              {
                  $data=array('vehicle_type'=>$request->vehicle_type,'vehicle_type_id'=>$request->vehicle_type_id,"vehicle_name"=>$request->vehicle_name,"vehicle_number"=>$request->vehicle_number,"basic_charge"=>$request->dTd_basicCharge,
                    "basic_km"=>$request->dTd_basic_KM,"extra_charge"=>$request->dTd_extraCharge);
         
        
                  DB::table('dTd_charges')->insert($data);
              }
              else
              {
                  //dd(array('vehicle_type'=>$request->vehicle_type,'vehicle_type_id'=>$request->vehicle_type_id,"vehicle_name"=>$request->vehicle_name,"vehicle_number"=>$request->vehicle_number,"basic_charge"=>$request->dTd_basicCharge,
                   // "basic_km"=>$request->dTd_basic_KM,"extra_charge"=>$request->dTd_extraCharge));
                  
                  $data = DB::table('dTd_charges')->where('id',$request->charge_id)->update(array('vehicle_type'=>$request->vehicle_type,'vehicle_type_id'=>$request->vehicle_type_id,"vehicle_name"=>$request->vehicle_name,"vehicle_number"=>$request->vehicle_number,"basic_charge"=>$request->dTd_basicCharge,
                    "basic_km"=>$request->dTd_basic_KM,"extra_charge"=>$request->dTd_extraCharge)); 
                    
              }
             
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
    
}