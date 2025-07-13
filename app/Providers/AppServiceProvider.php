<?php

namespace App\Providers;

use App\Services\NamingService;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        URL::forceScheme('https');

        FilamentAsset::register([
            Css::make('tailwind', Vite::asset('resources/css/app.css')),
        ]);
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(NamingService::class, fn() => new NamingService);
    }
}
