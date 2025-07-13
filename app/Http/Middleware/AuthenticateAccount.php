<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateAccount
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
        if ($request->has('account')) {
            Log::info('Logging in ' . $request->get('account'));

            $decoded = base64_decode(Str::of($request->header('Authorization'))
                ->after('Basic ')
                ->toString());

            $credentials = [
                'email' => Str::of($decoded)->before(':')->toString(),
                'password' => Str::of($decoded)->after(':')->toString(),
            ];

            if ($request->get('account') != $credentials['email']) {
                Log::error('Account doesn\'t match Basic Auth. Aborting.');
                abort(400);
            }

            if (!Auth::attempt($credentials)) {
                Log::error('Authentication failed.');
                abort(401);
            }

            $user = Auth::user();

            Log::info('Successfully authenticated user.', [
                'name' => $user->name,
                'email' => $user->email,
            ]);
        } else {
            Log::info('No account provided. Aborting.');
            abort(400);
        }

        return $next($request);
    }
}
