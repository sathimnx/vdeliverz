<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class CheckVerifiedVendor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Auth::check() && (!auth('api')->user()->active)){
            // auth('api')->user()->AauthAcessToken()->where('name', 'vendor_token')->delete();
            //  $VappData = [
            //         'fcm' => auth('api')->user()->fcm,
            //         'title' => 'Account Disabled!',
            //         'banned' => true,
            //         'body' => 'Please Contact Admin.',
            //         'icon' => '',
            //         'type' => 1,
            //     ];
            //     sendSingleAppNotification($VappData, env('VEN_FCM'));
                auth('api')->user()->update(['fcm' => null]);
            return response(['status' => false, 'message' => 'Accound not approved contact admin!', 'banned' => true], 403);
        }
        return $next($request);
    }
}