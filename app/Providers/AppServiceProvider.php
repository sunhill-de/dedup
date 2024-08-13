<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Sunhill\Dedup\FilterManager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->singleton(FilterManager::class, function () { return new FilterManager(); } );
        $this->app->alias(FilterManager::class,'filters');
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
