<?php
namespace App\Functions\Providers;

use Illuminate\Support\ServiceProvider;
use App\Functions\ToolFactory;

class ToolServiceProvider extends ServiceProvider
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
        $this->app->singleton(ToolFactory::class, function () {
            return new ToolFactory();
        });
    }
}
