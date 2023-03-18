<?php

namespace App\Http\Controllers\Api\Demand\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, DB, Auth, Session;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function addCarWishlist(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'car_provider_id' => 'required',
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            $wishlist = \App\Wishlist::where('user_id', Auth::user()->id)->where('car_provider_id', $request->car_provider_id)->first();
            if($wishlist){
                $wishlist->delete();
                return response()->json(['status' => true, 'message' => 'Car deleted from Wishlist', 'is_wishlist' => false]);
            }else{
                \App\Wishlist::create(['user_id' => Auth::user()->id, 'car_provider_id' => $request->car_provider_id]);
                return response()->json(['status' => true, 'message' => 'Car added to Wishlist', 'is_wishlist' => true]);
            }
            return response()->json(['status' => true, 'message' => 'Invalid Car', 'is_wishlist' => false]);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            dd($th);
        }
    }
    public function addWishlist(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'provider_id' => 'required',
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            $wishlist = \App\Wishlist::where('user_id', Auth::user()->id)->where('provider_id', $request->provider_id)->first();
            if($wishlist){
                $wishlist->delete();
                return response()->json(['status' => true, 'message' => 'Provider deleted from Wishlist', 'is_wishlist' => false]);
            }else{
                \App\Wishlist::create(['user_id' => Auth::user()->id, 'provider_id' => $request->provider_id]);
                return response()->json(['status' => true, 'message' => 'Provider added to Wishlist', 'is_wishlist' => true]);
            }
            return response()->json(['status' => true, 'message' => 'Invalid Provider', 'is_wishlist' => false]);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            dd($th);
        }
    }

    public function wishlists(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'latitude' => 'required',
                'longitude' => 'required',
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }
            if(!auth('api')->user()){
                return response(['status' => false, 'message' => 'Unauthorized!']);
            }
            $providers = auth('api')->user()->providers;
            $result = [];
            foreach ($providers as $key => $provider) {
                $data = canShowShop($provider->latitude, $provider->longitude, $request->latitude, $request->longitude);
                if($data['int_dis'] <= $provider->radius){
                    $single = [
                        'provider_id' => $provider->id,
                        'provider_name' => $provider->name,
                        'provider_image' => $provider->image,
                        'provider_area' => $provider->area,
                        'provider_address' => $provider->street.', '.$provider->area.', '.$provider->city,
                        'hour_price' => $provider->hour,
                        'job_price' => $provider->job,
                        'distance' => $data['distance'],
                        'rating' => $provider->rating_avg,
                        'total_rating' => $provider->rating_count,
                        'available' => $provider->is_available
                    ];
                    $result[] = $single;
                }
            }
            return response()->json(['status' => true, 'message' => 'Wishlist Datas', 'services' => $result]);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            dd($th);
        }
    }

    public function demandNotifications(){
        $user = Auth::user();
        $data = [];
        foreach ($user->unreadNotifications()->where('data->demand', true)->get() as $key => $value) {
            if(isset($value->data['booking_id'])){
                $booking = \App\Booking::where('id', $value->data['booking_id'])->first();
                $notify = [
                    "notify_id" => $value->notifiable_id,
                    "notify_head" => $booking->referral ?? NULL,
                    'description' => 'Service Booked Successfully.' ?? NULL,
                    'image' => env('APP_URL').config('constants.notify').'service_booked.png',
                    'time' => Carbon::createFromTimeStamp(strtotime($value->created_at))->diffForHumans()
                    ];
                    array_push($data, $notify);
            }
        }
        return response(['status' => true, 'message' => 'Demand Notifications', 'data' => $data]);
    }
}
