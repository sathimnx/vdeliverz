<?php

namespace App\Http\Controllers\Api\vendor\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, DB, Auth, Session, Hash, Mail;
use App\User;
use App\Mail\SendLoginCode;
use App\Mail\RegisterSuccess;
use App\Notifications\VerificationSuccess;
use App\Mail\AdminRegisterSuccess;
use App\Mail\VendorRegisterSuccess;

class LoginController extends Controller
{
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
            'fcm' => 'required'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $mobile = str_replace('+91', '', $request->email);
        $mobile = '+91'.$mobile;
        $user = \App\User::where('email', $request->email)->orWhere('mobile', $mobile)->first();
        if(!$user || !$user->hasAnyRole('vendor')){
            return response(['status' => false, 'message' => 'User not found!']);
        }
        // if($user->AauthAcessToken()->where('name', 'vendor_token')->first()){
        //     $user->AauthAcessToken()->where('name', 'vendor_token')->delete();
        // }
        // if(!$user->hasAnyRole('vendor')){
        //     $user->assignRole('vendor');
        // }
        // if($user->verified == 0 || !$user->email_verified_at){
        //     return response(['status' => false, 'message' => 'Profile details not verified!', 'mobile' => $user->mobile, 'email' => $user->email, 'mobile_verified' => $user->verified, 'email_verified' => $user->email_verified_at ? 1 : 0]);
        // }
        if($user->active == 0){
            return response(['status' => false, 'message' => 'Waiting for confirmation from Admin!']);
        }
        if($user && Hash::check($request->password, $user->password)){
            $user->update(['fcm' => $request->fcm]);
            // $user->AauthAcessToken()->delete();
            $token =  $user->createToken('vendor_token')->accessToken;
            return response()->json(['status' => true, 'message' => 'User Logged in', 'user' => $user, 'token' => $token]);
        }
        return response(['status' => false, 'message' => 'Invalid Credentials!']);
    }

    public function register(Request $request){
            $mobile = str_replace('+91', '', $request->mobile);
            $request->merge(['mobile' => '+91'.$mobile]);
            $validator = Validator::make($request->all(), [
                'name' => 'bail|required|min:2',
                'email' => 'required|email|unique:users',
                'mobile' => 'required|unique:users',
                'password' => 'required|min:6',
                'confirm_password' => 'required_with:password|same:password',
            ]);
            if($validator->fails()){
                $errors = implode(" & ", $validator->errors()->all());
                return response(['status' => false, 'message' => $errors]);
            }

            $user = new User;
            $user->password =  Hash::make($request->get('password'));
            $user->active = 0;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->save();
            $user->assignRole('vendor');

            // $send = sendLoginOTP($user->mobile, $otp);
            return response(['status' => true, 'message' => 'Registered Successfully.']);

    }

    public function otpVerify(Request $request){

        $validator = Validator::make($request->all(), [
            'mobile' => 'required',
            'otp' => 'required',
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $user = \App\User::where('mobile', $request->mobile)->where('otp', $request->otp)->first();
        if(!$user){
            return response(['status' => false, 'message' => 'Invalid OTP!']);
        }
        $user->verified = 1;
        $user->save();
        return response(['status' => true, 'message' => 'OTP Verified Successfully.']);
    }

    public function mailVerify(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required',
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $user = \App\User::where('email', $request->email)->where('otp', $request->otp)->first();
        if(!$user){
            return response(['status' => false, 'message' => 'Invalid OTP!']);
        }
        $user->email_verified_at = now();
        $user->save();
        $data = [
            'subject' => 'VDeliverz New Vendor Registered.',
            'admin_content' => 'New Vendor Registration',

            'vendor_content' => '   Welcome to be part of VDeliverz team. We are happy to see you here. You will receive your activation mail from VDeliverz Admin within 24hrs. Kindly be Patience. In case of missed communication kindly contact our Admin Support @ +91 75988 97020 or Mail us to hello@VDeliverzdelivery.com',

                'vendor_name' => $user->name,
                'table' => [
                    'Registered Vendor' => $user->name,
                    'Vendor Mobile' => $user->mobile,
                    'Vendor Email' => $user->email,
                    'Registered Date' => $user->created_at->format('d-m-Y H:i')
                ]
            ];

        Mail::to(config('constants.admin_email'))->send(new AdminRegisterSuccess($data));
        Mail::to($user->email)->send(new VendorRegisterSuccess($data));
        // $user->notify(new VerificationSuccess);
        return response(['status' => true, 'message' => 'Email Verified Successfully.']);
    }

    public function resendOTP(Request $request){
        $validator = Validator::make($request->all(), [
            'mobile' => 'required',
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $user = \App\User::where('mobile', $request->mobile)->first();
        if(!$user){
            return response(['status' => false, 'message' => 'Account not found!']);
        }
        $otp = rand(1000, 9999);
        $user->otp = $otp;
        $user->save();
        $send = sendDynamicOTP($user->mobile, $otp);
        return response(['status' => true, 'message' => 'OTP sent Successfully.', 'otp' => 'OTP sent Successfully.']);
    }

    public function sendMailOtp(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $user = \App\User::where('email', $request->email)->first();
        if(!$user){
            return response(['status' => false, 'message' => 'Account not found!']);
        }
        $otp = rand(1000, 9999);
        $user->otp = $otp;
        $user->save();
        $data = ['otp' => $otp, 'name' => ucfirst($user->name)];
        Mail::to($user->email)->send(new SendLoginCode($data));
        return response(['status' => true, 'message' => 'OTP sent Successfully.', 'otp' => 'OTP sent Successfully.']);
    }
    public function resetPassword(Request $request){
        $validator = Validator::make($request->all(), [
            'mobile' => 'required',
            'password' => 'required|min:6',
            'confirm_password' => 'required_with:password|same:password',
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }

        $user = \App\User::role('vendor')->where('mobile', $request->mobile)->first();
        if(!$user){
            return response(['status' => false, 'message' => 'User not found!']);
        }
        $user->password =  Hash::make($request->get('password'));
        $user->save();
        // $send = sendLoginOTP($user->mobile, $otp);
        return response(['status' => true, 'message' => 'Registered Successfully.']);

}

public function sendMail(Request $request){
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
    ]);
    if($validator->fails()){
        $errors = implode(" & ", $validator->errors()->all());
        return response(['status' => false, 'message' => $errors]);
    }

    $data = ['content' => config('constants.register_success'), 'name' => 'Sample Vendor', 'subject' => 'Registered Successfully'];
    Mail::to('selselvatocodefo@gmail.com')->send(new RegisterSuccess($data));
    return response(['status' => true, 'message' => 'OTP sent Successfully.']);
}
}