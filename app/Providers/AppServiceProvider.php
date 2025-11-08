<?php

namespace App\Providers;

use App\Services\NamingService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        URL::forceScheme('https');
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(NamingService::class, fn() => new NamingService);
    }
}
