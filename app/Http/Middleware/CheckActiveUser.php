<?php

namespace App\Http\Middleware;

use Closure;

class CheckActiveUser
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
        if(auth('api')->user() && (!auth('api')->user()->active)){
            // auth('api')->user()->AauthAcessToken()->where('name', 'customer_token')->delete();
            //  $VappData = [
            //         'fcm' => auth('api')->user()->fcm,
            //         'title' => 'Account Disabled!',
            //         'banned' => true,
            //         'body' => 'Please Contact Admin.',
            //         'icon' => '',
            //         'type' => 1,
            //     ];
            //     sendSingleAppNotification($VappData, env('CUS_FCM'));
            auth('api')->user()->update(['fcm' => null]);
            return response(['status' => false, 'message' => 'Accound Banned. contact admin!', 'banned' => true], 403);
        }
        return $next($request);
    }
}