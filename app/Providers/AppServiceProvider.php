<?php

namespace App\Providers;

use URL;
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
        if (config('app.env') === 'production') {
            // asset()やurl()がhttpsで生成される
            // URL::forceScheme('http');
            URL::forceScheme('https');

            // $appUrl = empty($_SERVER["HTTPS"]) ? "http://" . url('/') : "https://" . url('/');
            URL::forceRootUrl(config('app.url'));
            \Config::set('app.url', url('/'));

            if (empty(url('/'))) {
                \Config::set('app.asset_name', "app");
            }

            if (strpos(url()->current(),'https://eco-hack.work') !== false){
                \Config::set('app.asset_name', "work");
            }

            if (strpos(url()->current(),'https://eco-hack.me') !== false){
                \Config::set('app.asset_name', "me");
            }
        }
    }
}
