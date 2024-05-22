<?php

use App\Facades\JWT;
use App\Http\Controllers\TokenController;
use App\Http\Middleware\AuthenticateAccount;
use App\Http\Middleware\HasValidNotifyToken;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Lcobucci\JWT\Token\Builder;

Route::get('/auth/token', [TokenController::class, 'entry'])
    ->middleware(AuthenticateAccount::class);

Route::post('/notify', function (Request $request) {
    Log::channel('stderr')->info($request->all());
    return response('Thanks!', 200);
})->middleware(HasValidNotifyToken::class)
    ->withoutMiddleware(VerifyCsrfToken::class);

Route::get('/generateToken', function (Request $request) {
    $token = JWT::generalToken(function (Builder $builder) {
        return $builder
            ->issuedBy(config('registry.auth_name'))
            ->permittedFor(config('registry.registry_name'))
            ->withClaim('access', 'notify');
    });

    return $token->toString();
});

