<?php

namespace App\Providers;

use App\Models\Activity;
use App\Models\ImageConfig;
use App\Models\ImageLayer;
use App\Models\Manifest;
use App\Models\ManifestListEntry;
use App\Models\Member;
use App\Models\Repository;
use App\Models\Space;
use App\Models\Tag;
use App\Models\User;
use App\Models\Webhook;
use App\Services\CurrentSpaceService;
use App\Services\NamingService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }

        RateLimiter::for('docker-auth', function (Request $request) {
            $key = strtolower((string) $request->query('account', '')).'|'.$request->ip();

            return Limit::perMinute(10)->by($key);
        });

        Route::model('space', Space::class);

        Route::bind('repository', function (string $value, \Illuminate\Routing\Route $route) {
            $spaceParam = $route->parameter('space');

            $space = $spaceParam instanceof Space
                ? $spaceParam
                : Space::where('namespace', $spaceParam)->firstOrFail();

            return $space->repositories()
                ->where('name', $value)
                ->firstOrFail();
        });

        Relation::enforceMorphMap([
            'user' => User::class,
            'repository' => Repository::class,
            'webhook' => Webhook::class,
            'manifest' => Manifest::class,
            'tag' => Tag::class,
            'image_config' => ImageConfig::class,
            'image_layer' => ImageLayer::class,
            'manifest_list_entry' => ManifestListEntry::class,
            'space' => Space::class,
            'member' => Member::class,
            'activity' => Activity::class,
        ]);
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(NamingService::class);
        $this->app->scoped(CurrentSpaceService::class);
    }
}
