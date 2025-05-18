<?php

namespace App\Http\Middleware;

use Cainy\Dockhand\Facades\TokenService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Lcobucci\JWT\Validation\Constraint\HasClaimWithValue;
use Symfony\Component\HttpFoundation\Response;

class HasValidNotifyToken
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response) $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::channel('stderr')->info('Notify: Trying to verify token...');

        if (!$request->bearerToken()) {
            Log::channel('stderr')->error('Notify: Missing token.');
            abort(401);
        }

        if (!TokenService::validateToken($request->bearerToken(), function ($validator, $token) {
            $validator->assert($token, new HasClaimWithValue('access', 'notify'));
        })) {
            Log::channel('stderr')->error('Notify: Faulty token.');
            abort(401);
        }

        Log::channel('stderr')->info('Notify: Token verified!');
        return $next($request);
    }
}
