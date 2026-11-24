<?php

namespace App\Providers;
use App\BerasPengambilan;
use App\Support\PmsNav;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
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

        // Konteks menu PMS (header + subnav) dihitung sekali per request.
        View::composer('layouts.header', function ($view) {
            $shared = view()->shared('permissions');

            $view->with('pmsNav', PmsNav::context(null, is_array($shared) ? $shared : null));
        });
    }
}
