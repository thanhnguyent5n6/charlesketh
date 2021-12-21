<?php

namespace App\Functions\Providers;

use Illuminate\Support\ServiceProvider;
use App\Functions\MenuFactory;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(MenuFactory::class, function ($app) {
            return new MenuFactory();
        });
    }
}
