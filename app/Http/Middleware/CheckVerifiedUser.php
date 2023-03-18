<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class CheckVerifiedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if(Auth::check() && (!auth()->user()->active)){
            Auth::logout();
            flash()->error('Account not approved contact admin!');
            return redirect()->route('login');
        }
        return $next($request);
    }
}
