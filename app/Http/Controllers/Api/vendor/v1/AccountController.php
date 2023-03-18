<?php

namespace App\Http\Controllers\Api\vendor\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, DB, Session;

class AccountController extends Controller
{
    //Logout
    public function logoutApi()
    {
            auth('api')->user()->AauthAcessToken()->where('name', 'vendor_token')->delete();
            auth('api')->user()->update(['fcm' => null]);
            return response()->json(['status' => true, 'message' => 'Successfully Logged out']);
    }

    public function user(){
        return response()->json(['status' => true, 'message' => 'User Data', 'user' => \App\User::where('id', auth('api')->user()->id)->first(['id', 'name', 'email', 'mobile', 'image'])]);
    }

    public function userUpdate(Request $request){
        $mobile = str_replace('+91', '', $request->mobile);
        $request->merge(['mobile' => '+91'.$mobile]);
        $validator = Validator::make($request->all(), [
                'mobile' => 'required|unique:users,mobile,'.auth('api')->user()->id,
                'name' => 'required',
                "email" => "unique:users,email,".auth('api')->user()->id,
                "profile_image" => 'image'
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors]);
            }

            $user = auth('api')->user();
            if($request->hasFile('profile_image')){
                if($request->file('profile_image')->isValid())
                {

                    $extension = $request->profile_image->extension();
                    $file_path = $request->email.time()."user." .$extension;
                    $request->profile_image->move(config('constants.user_profile_img'), $file_path);
                    $user->image = $file_path;
                }
            }
            $user->name = $request->name;
            $user->mobile = $request->mobile;
            $user->email = $request->email;
            $user->save();
            return response()->json(['status' => true, 'message' => 'User Updated']);
    }

    public function notifications(){
        $notifications = auth('api')->user()->unreadNotifications()->select('notifications.*')->where('type', 'App\Notifications\AdminOrder')->paginate(10);
        foreach ($notifications as $key => $value) {
            if($value->data['admin_order_id']){
                $order = \App\Order::find($value->data['admin_order_id']);
                if($order){
                    $data[] = [
                        "notify_id" => $value->id,
                        "notify_head" => 'New Order arrived!',
                        'image' => env('APP_URL').config('constants.notify').'placed.png',
                        'description' => 'Order ID : '.$order->search,
                        'time' => \Carbon\Carbon::createFromTimeStamp(strtotime($value->created_at))->diffForHumans()
                    ];
                }
            }
        }
        $pagination = apiPagination($notifications);
        return response()->json(['status' => true, 'message' => 'Notification Data.', 'data' => $data ?? [], 'pagination' => $pagination]);
    }
}
