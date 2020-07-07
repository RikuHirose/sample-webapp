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
        }
    }
}
