<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
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

    }
    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        
        $this->app->singleton(FakerGenerator::class, function () {
            return FakerFactory::create('id_ID');
        });

    }
}
