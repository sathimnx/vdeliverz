<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if(env('APP_ENV') === 'production') {
            \URL::forceScheme('https');
        }
    //    view()->composer('*', function($view){
    //     $notifications = auth()->user() ? auth()->user()->unreadNotifications()->select('notifications.*')->where('type', 'App\Notifications\AdminOrder')->take(5)->get() : [];
    //     $demand = auth()->user() ? auth()->user()->unreadNotifications()->select('notifications.*')->where('data->demand', true)->take(5)->get() : [];
    //          $view->with([
    //              'admin_notifications' => $notifications,
    //              'demand_notifications' => $demand
    //          ]);
    //    });
    }
}
