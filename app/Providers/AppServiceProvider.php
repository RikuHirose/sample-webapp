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
        URL::forceRootUrl(config('app.url'));

        if (config('app.env') === 'production') {
            // asset()やurl()がhttpsで生成される
            // URL::forceScheme('http');
            URL::forceScheme('https');

            $appUrl = empty($_SERVER["HTTPS"]) ? "http://" . url('/') : "https://" . url('/');

            \Config::set('app.url', $appUrl);

            if (empty(url('/'))) {
                \Config::set('app.asset_name', "app");
            }

            if (url('/') === "eco-hack.work/") {
                \Config::set('app.asset_name', "work");
            }

            if (url('/') === "eco-hack.me/") {
                \Config::set('app.asset_name', "me");
            }
        }
    }
}
