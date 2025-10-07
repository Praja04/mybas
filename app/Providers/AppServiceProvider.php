<?php

namespace App\Providers;
use App\BerasPengambilan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Observers\BerasPengambilanObserver;

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
        // BerasPengambilan::observe(BerasPengambilanObserver::class);
        Schema::defaultStringLength(191);
    }
}
