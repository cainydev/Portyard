<?php

use App\Http\Controllers\TokenController;
use App\Http\Middleware\AuthenticateAccount;
use App\Models\Tag;
use App\Models\User;
use Cainy\Dockhand\Facades\Dockhand;
use Cainy\Dockhand\Facades\Scope;
use Cainy\Dockhand\Facades\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/** Flexible Home */
Route::get('/', function () {
    return app()->call([
        app('livewire')->new(Auth::check() ? 'pages::app.dashboard' : 'pages::website.home'),
        '__invoke',
    ]);
})->name('root');

/** App */
Route::middleware('auth')
    ->name('app.')
    ->group(function () {
        /** Repositories */
        Route::prefix('repositories')
            ->name('repositories.')
            ->group(function () {
                Route::livewire('/', 'pages::app.repositories.list')
                    ->name('list');

                Route::livewire('new', 'pages::app.repositories.new')
                    ->name('new');

                Route::prefix('{user:namespace}/{repository:name}')
                    ->scopeBindings()
                    ->group(function () {
                        Route::livewire('/', 'pages::app.repositories.overview')
                            ->name('overview');

                        Route::livewire('settings', 'pages::app.repositories.settings')
                            ->name('settings');

                        Route::livewire('tags', 'pages::app.repositories.tags')
                            ->name('tags');

                        Route::livewire('collaborators', 'pages::app.repositories.collaborators')
                            ->name('collaborators');

                        Route::livewire('webhooks', 'pages::app.repositories.webhooks')
                            ->name('webhooks');
                    });
            });

        /** Settings */
        Route::prefix('settings')
            ->name('settings.')
            ->group(function () {
                Route::redirect('/', 'profile');

                Route::livewire('profile', 'pages::app.settings.profile')
                    ->name('profile');

                Route::livewire('password', 'pages::app.settings.password')
                    ->name('password');

                Route::livewire('appearance', 'pages::app.settings.appearance')
                    ->name('appearance');

                Route::livewire('two-factor', 'pages::app.settings.two-factor')
                    ->middleware(['password.confirm'])
                    ->name('two-factor');
            });
    });

/** Website */
Route::livewire('/home', 'pages::website.home')->name('website.home');
Route::livewire('/features', 'pages::website.features')->name('website.features');
Route::livewire('/open-source', 'pages::website.oss')->name('website.oss');
Route::livewire('/docs', 'pages::website.docs')->name('website.docs');
Route::livewire('/pricing', 'pages::website.pricing')->name('website.pricing');

Route::get('/test-tag', function () {
    return Tag::all();
});

Route::get('/auth/token', [TokenController::class, 'entry'])
    ->middleware(AuthenticateAccount::class);

Route::get('/status', function (Request $request) {
    return [
        'online' => Dockhand::isOnline(),
        'version' => Dockhand::getApiVersion()->value,
        'catalog' => Dockhand::getRepositories(),
    ];
});

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
