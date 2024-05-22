<?php

namespace App\Http\Middleware;

use App\Facades\JWT;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Lcobucci\JWT\Token\Builder;
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
            Log::channel('stderr')->info('Logging in ' . $request->get('account'));

            $decoded = base64_decode(Str::of($request->header('Authorization'))
                ->after('Basic ')
                ->toString());

            $credentials = [
                'email' => Str::of($decoded)->before(':')->toString(),
                'password' => Str::of($decoded)->after(':')->toString(),
            ];

            if ($request->get('account') != $credentials['email']) {
                Log::channel('stderr')->error('Account doesn\'t match Basic Auth. Aborting.');
                abort(400);
            }

            if (!Auth::attempt($credentials)) {
                Log::channel('stderr')->error('Authentication failed.');
                abort(401);
            }

            Log::channel('stderr')->info('Login successful.');

            $token = JWT::createToken(function (Builder $builder) {
                return $builder
                    ->issuedBy(config('registry.auth_name'))
                    ->permittedFor(config('registry.registry_name'))
                    ->relatedTo(Auth::user()->id)
                    ->withClaim('access', [
                        [
                            'type' => 'registry',
                            'class' => '',
                            'name' => 'catalog',
                            'actions' => ['*']
                        ]
                    ]);
            });

            $request->attributes->set('catalog_token', $token->toString());
        }

        return $next($request);
    }
}
