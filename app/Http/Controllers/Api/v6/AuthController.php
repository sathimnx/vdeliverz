<?php

namespace App\Http\Controllers\Api\v6;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB, Validator, Auth, Hash;
use \Spatie\Permission\Models\Role;
use \Spatie\Permission\Models\Permission;
use App\User;
use Twilio\Rest\Client;
use Twilio\Jwt\ClientToken;

class AuthController extends Controller
{
    public $successStatus = 200;

    public function login(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                    'mobile' => 'required',
                    'name' => 'required'
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
                }
                if($request->mobile == '+910000000000'){
                    $otp = 1111;
                    return response()->json(['status' => true, 'message' => 'OTP sent Successfully', 'otp'=> $otp, 'mobile' => '+910000000000']);
                }
                $user = \App\User::where('mobile', $request->mobile)->where('active', 1)->first();

                $otp = rand(1000, 9999);
                if(!$user){
                    $user = new \App\User;
                    $user->mobile = $request->mobile;
                }
                $user->name = $request->name;
                $user->otp = $otp;
                $send = sendDynamicOTP($user->mobile, $otp);
                if(!$send){
                    return response()->json(['status' => false, 'message' => 'OTP send Failed!. Please try again.']);
                }
                $user->save();
                $user->assignRole('customer');
                return response()->json(['status' => true, 'message' => 'OTP sent Successfully', 'otp'=> 'OTP Sent Successfully.', 'mobile' => $user->mobile]);
        } catch (\Throwable $th) {
            // return apiCatchResponse();
            dd($th);
        }
    }
 public function verifyLogin(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                    'mobile' => 'required'
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
                }
                $user = \App\User::where('mobile', $request->mobile)->where('active', 1)->first();
                $otp = rand(1000, 9999);
                $user->otp = $otp;
                $user->save();
                $send = sendDynamicOTP($request->mobile, $otp);
                if(!$send){
                    return response()->json(['status' => false, 'message' => 'OTP send Failed!. Please try again.']);
                }
                
                return response()->json(['status' => true, 'message' => 'OTP sent Successfully', 'otp'=> 'OTP Sent Successfully.', 'mobile' => $user->mobile]);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            dd($th);
        }
    }
    
    
    
    public function verifyLoginOtp(Request $request){
        try{
            $validator = Validator::make($request->all(), [
            'otp' => 'required',
            'mobile' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
            }
            $user = \App\User::where('mobile', $request->mobile)->first();
           
            if($user->otp == $request->otp){
                return response()->json(['status' => true, 'message' => 'User Found', 'user' => $user]);
            }
            else{
            return response()->json(['status' => false, 'message' => 'Invalid OTP!']);
            }
            
        } catch (\Throwable $th) {
          //  return apiCatchResponse();
            dd ($th);
        }
    }

    public function deliveryLogin(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                    'email' => 'required',
                    'password' => 'required',
                    "fcm_token" => 'required'
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
                }
                $user = \App\User::role('delivery-boy')->where('email', $request->email)->where('active', 1)->first();
                if(!$user){
                    return response()->json(['status' => false, 'message' => 'Invalid Login! Please contact admin']);
                }
                // if($user->AauthAcessToken()->where('name', 'delivery_token')->first()){
                //     $user->AauthAcessToken()->where('name', 'delivery_token')->delete();
                // }
                $user->update(['fcm' => $request->fcm_token]);
                if(Hash::check($request->password, $user->password)){
                    $success['token'] =  $user->createToken('delivery_token')->accessToken;
                    return response()->json(['status' => true, 'message' => 'User Logged in', 'user' => $user, 'token' => $success['token']], $this->successStatus);
                }
                return response()->json(['status' => true, 'message' => 'Invalid Credentials!']);
        } catch (\Throwable $th) {
           // return apiCatchResponse();
            dd($th);
        }
    }

    public function appleLogin(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required',
                'username' => 'required',
                "device_id" => 'required',
                "fcm_token" => 'required'
            ]);
            if($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
            }
            $user = User::where(['email' => $request->email])->first();
            if($user){
                $moved = moveCartToUser($request->device_id, $user);
                $token =  $user->createToken('VmartApp')->accessToken;
                return response(['status' => true, 'message' => 'Login Successfully.', 'user' => $user, 'token' => $token]);
            }else{
                $user = User::create([
                    'name'          => $request->username,
                    'email'         => $request->email,
                    'password'      => Hash::make($request->email),
                    'fcm'  => $request->fcm_token,
                    'provider_id'   => 'apple_id',
                    'provider_name'      => 'Apple login',
                ]);
                $user->assignRole('customer');
                $moved = moveCartToUser($request->device_id, $user);
                $token =  $user->createToken('VmartApp')->accessToken;
                return response(['status' => true, 'message' => 'Login Successfully', 'user' => $user, 'mobile' => $user->mobile, 'token' => $token]);
            }
            return response()->json(['status' => false, 'message' => 'Invalid Login!']);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            dd($th);
        }
    }

    public function verifyOTP(Request $request){
        try {
            $validator = Validator::make($request->all(), [
            'otp' => 'required',
            'mobile' => 'required',
            'device_id' => 'required',
            "fcm_token" => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
            }
            DB::beginTransaction();
            $user = \App\User::where('mobile', $request->mobile)->first();
            if($request->mobile == '+910000000000' && $user->otp == $request->otp){
                $user->fcm = $request->fcm_token;
                $user->save();
                $moved = moveCartToUser($request->device_id, $user);
                $success['token'] =  $user->createToken('customer_token')->accessToken;
                DB::commit();
                return response()->json(['status' => true, 'message' => 'User Found', 'user' => $user, 'token' => $success['token']], $this->successStatus);
            }
            if($user && $user->otp == $request->otp){
                $user->fcm = $request->fcm_token;
                $user->save();
                $moved = moveCartToUser($request->device_id, $user);
                $success['token'] =  $user->createToken('customer_token')->accessToken;
                DB::commit();
                return response()->json(['status' => true, 'message' => 'User Found', 'user' => $user, 'token' => $success['token']], $this->successStatus);
            }
            return response()->json(['status' => false, 'message' => 'Invalid OTP!']);
        } catch (\Throwable $th) {
            DB::rollback();
            return apiCatchResponse();
            dd($th);
        }
    }

    public function socialLogin(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required',
                'id' => 'required',
                'provider_name' => 'required',
                "device_id" => 'required',
                "fcm_token" => 'required'
            ]);
            if($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
            }
            $user = User::where(['email' => $request->email])->first();
            if($user){
                $moved = moveCartToUser($request->device_id, $user);
                $token =  $user->createToken('customer_token')->accessToken;
                return response(['status' => true, 'message' => 'Login Successfully.', 'user' => $user, 'token' => $token]);
            }else{
            $user = User::create([
                    'name'          => $request->name,
                    'email'         => $request->email,
                    'password'      => Hash::make($request->id),
                    'fcm'  => $request->fcm_token,
                    'provider_id'   => $request->id,
                    'provider_name'      => $request->provider_name,
                ]);
                $user->assignRole('customer');
                $moved = moveCartToUser($request->device_id, $user);
                $token =  $user->createToken('customer_token')->accessToken;
                return response(['status' => true, 'message' => 'Login Successfully', 'user' => $user, 'mobile' => $user->mobile, 'token' => $token]);
            }
            return response()->json(['status' => false, 'message' => 'Invalid Login!']);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            dd($th);
        }
    }

    // public function resendOTP(Request $request){
    //     $accountSid = config('app.twilio')['TWILIO_ACCOUNT_SID'];
    //     $authToken  = config('app.twilio')['TWILIO_AUTH_TOKEN'];
    //     $appSid     = config('app.twilio')['TWILIO_APP_SID'];
    //     $client = new Client($accountSid, $authToken);
    //     try
    //     {
    //         $client->messages->create(
    //             '+919360741015',
    //        array(
    //              'from' => '+13476097357',
    //              'body' => 'Hey Ketav! It’s good to see you after long time!'
    //          )
    //      );
    //     }
    //     catch (Exception $e)
    //     {
    //         echo "Error: " . $e->getMessage();
    //     }
    // }

    public function resendOTP(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'mobile' => 'required'
            ]);
            if($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
            }
            $user = \App\User::where('mobile', $request->mobile)->where('active', 1)->first();
            if(!$user){
                return response()->json(['status' => false, 'message' => 'User not found']);
            }
            $otp = rand(1000, 9999);
            $user->otp = $otp;
            $send = sendDynamicOTP($user->mobile, $otp);
            if(!$send){
                return response()->json(['status' => false, 'message' => 'OTP send Failed!. Please try again.']);
            }
            $user->save();
            return response()->json(['status' => true, 'message' => 'OTP sent successfully', 'otp' => 'OTP sent successfully']);
        } catch (\Throwable $th) {
            return apiCatchResponse();
            //throw $th;
        }
    }

    public function decimalNumber(){
        $toppings = \App\Topping::all();
        $stocks = \App\Stock::all();
        foreach ($toppings as $key => $value) {
            $value->update(['price' => number_format($value->price, 2)]);
        }
        foreach ($stocks as $key => $value) {
            $value->update(['price' => number_format($value->price, 2), 'currency' => '₹', 'actual_price' => number_format($value->actual_price, 2)]);
        }
        return 'Changed.';
    }

}
