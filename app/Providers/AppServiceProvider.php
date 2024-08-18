<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Sunhill\Dedup\FilterManager;
use Sunhill\Dedup\Facades\Filters;
use Sunhill\Dedup\FileFilters\KnownFile_Ignore;
use Sunhill\Dedup\FileFilters\NewFile_Ignore;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->singleton(FilterManager::class, function () { return new FilterManager(); } );
        $this->app->alias(FilterManager::class,'filters');
        Filters::clearFilters();
        Filters::addFilters([
            KnownFile_Ignore::class,
            NewFile_Ignore::class
        ]);
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
             //
    }
}
