<?php

use App\Http\Controllers\TokenController;
use App\Http\Middleware\AuthenticateAccount;
use Cainy\Dockhand\Facades\Scope;
use Cainy\Dockhand\Facades\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/auth/token', [TokenController::class, 'entry'])
    ->middleware(AuthenticateAccount::class);

Route::get('/token', function (Request $request) {
    $token = $request->has('scope') ?
        Token::withScope(Scope::fromString($request->get('scope'))) :
        Token::create();

    $token = $token
        ->issuedBy(config('dockhand.authority_name'))
        ->expiresAt(now()->addMinutes(5))
        ->permittedFor(config('dockhand.registry_name'));

    return response()->json([
        'token' => $token->toString(),
        'payload' => $token->get()->payload(),
        'claims' => $token->get()->claims()->toString()
    ]);
});
