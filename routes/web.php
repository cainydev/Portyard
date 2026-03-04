<?php

use App\Http\Controllers\TokenController;
use App\Http\Middleware\AuthenticateAccount;
use App\Http\Middleware\EnsureSpaceAccess;
use App\Models\User;
use Cainy\Dockhand\Facades\Dockhand;
use Cainy\Dockhand\Facades\Scope;
use Cainy\Dockhand\Facades\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/auth/token', [TokenController::class, 'entry'])
    ->middleware(AuthenticateAccount::class);

if (! app()->isProduction()) {
    Route::get('/token', function (Request $request) {
        $token = $request->has('scope') ?
            Token::withScope(Scope::fromString($request->get('scope'))) :
            Token::create();

        $token = $token
            ->relatedTo(User::first()->email)
            ->issuedBy(config('dockhand.authority_name'))
            ->permittedFor(config('dockhand.registry_name'));

        return response()->json([
            'token' => $token->toString(),
            'payload' => $token->get()->payload(),
            'claims' => $token->get()->claims()->toString(),
        ]);
    });
}

Route::get('/', function () {
    if (auth()->check()) {
        $space = auth()->user()->spaces()->find(session('current_space_id'))
            ?? auth()->user()->spaces()->first();

        return $space
            ? redirect()->route('app.space.dashboard', $space)
            : redirect()->route('app.spaces.new');
    }

    return redirect()->route('website.home');
})->name('root');

Route::name('website.')->group(function () {
    Route::livewire('/home', 'pages::website.home')->name('home');
    Route::livewire('/features', 'pages::website.features')->name('features');
    Route::livewire('/open-source', 'pages::website.oss')->name('oss');
    Route::prefix('docs')->name('docs.')->group(function () {
        Route::livewire('/', 'pages::website.docs.overview')->name('overview');
        Route::livewire('/authentication', 'pages::website.docs.authentication')->name('authentication');
        Route::livewire('/github-actions', 'pages::website.docs.github-actions')->name('github-actions');
        Route::livewire('/docker-cli', 'pages::website.docs.docker-cli')->name('docker-cli');
    });
    Route::livewire('/pricing', 'pages::website.pricing')->name('pricing');
});

Route::middleware('auth')
    ->name('app.')
    ->group(function () {
        Route::prefix('spaces')->name('spaces.')->group(function () {

            // Create new Space
            Route::livewire('/new', 'pages::app.spaces.new')
                ->name('new');

            // The Interstitial Page for Context Switching
            Route::livewire('/switch', 'pages::app.spaces.switch')
                ->name('switch');
        });

        // User Settings (Not Space Specific)
        Route::prefix('settings')->name('user-settings.')->group(function () {
            Route::redirect('/', '/settings/profile');

            Route::livewire('/profile', 'pages::app.settings.profile')
                ->name('profile');

            Route::livewire('/security', 'pages::app.settings.security')
                ->middleware('password.confirm')
                ->name('security');

            Route::livewire('/appearance', 'pages::app.settings.appearance')
                ->name('appearance');
        });

        Route::prefix('{space}')
            ->middleware(EnsureSpaceAccess::class)
            ->name('space.')
            ->group(function () {
                Route::livewire('/', 'pages::app.space.dashboard')
                    ->name('dashboard');

                Route::livewire('/list', 'pages::app.space.repositories.list')
                    ->name('repositories.list');

                Route::livewire('/new', 'pages::app.space.repositories.new')
                    ->name('repositories.new');

                Route::livewire('/settings', 'pages::app.space.settings')
                    ->name('settings');

                // --- REPOSITORY CONTEXT ---
                // e.g. /my-org/my-repo/settings
                Route::prefix('{repository:name}')
                    ->name('repositories.')
                    ->scopeBindings()
                    ->group(function () {

                        Route::livewire('/', 'pages::app.space.repositories.overview')
                            ->name('overview');

                        Route::livewire('/settings', 'pages::app.space.repositories.settings')
                            ->name('settings');

                        Route::livewire('/collaborators', 'pages::app.space.repositories.collaborators')
                            ->name('collaborators');

                        Route::livewire('/webhooks', 'pages::app.space.repositories.webhooks')
                            ->name('webhooks');

                        Route::livewire('/tags', 'pages::app.space.repositories.tags')
                            ->name('tags');
                    });
            });
    });

Route::get('/status', function (Request $request) {
    return [
        'online' => Dockhand::isOnline(),
        'version' => Dockhand::getApiVersion()->value,
        'catalog' => Dockhand::getRepositories(),
    ];
});
