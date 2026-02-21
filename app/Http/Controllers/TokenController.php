<?php

namespace App\Http\Controllers;

use App\Models\Repository;
use Cainy\Dockhand\Enums\ScopeResourceType;
use Cainy\Dockhand\Facades\Scope;
use Cainy\Dockhand\Facades\Token;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use function abort;
use function array_filter;
use function config;
use function now;
use function response;

class TokenController extends Controller
{
    public function entry(Request $request)
    {
        $user = Auth::user();

        Log::info("New token request from {$user->name}!", $request->all());

        if (! $request->has('service') || $request->get('service') != config('dockhand.registry_name')) {
            Log::error('Request service didn\'t match registry name.');
            abort(400, 'Invalid service.');
        }

        if (! $request->has('scope')) {
            if ($request->has('account') && $request->get('account') !== $user->email) {
                Log::error('Account mismatch during login.');
                abort(401, 'Account mismatch.');
            }

            return response()->json([
                'token' => Token::create()
                    ->relatedTo($user->email)
                    ->expiresAt(now()->addMinutes(5))
                    ->issuedBy(config('dockhand.authority_name'))
                    ->permittedFor(config('dockhand.registry_name'))
                    ->toString(),
            ]);
        }

        try {
            $requestedScope = Scope::fromString($request->get('scope'));
        } catch (Exception $e) {
            Log::error('Failed to parse scope: '.$e->getMessage());
            abort(400, 'Invalid scope format.');
        }

        switch ($requestedScope->getResourceType()) {
            case ScopeResourceType::Registry:
                abort(401, 'Registry-level scopes are not supported for standard users.');

            case ScopeResourceType::Repository:
                $fullPath = $requestedScope->getResourceName();

                try {
                    $repository = Repository::fromPath($fullPath);

                    $actions = array_filter(
                        $requestedScope->getActions(),
                        fn (string $action) => $user->can($action, $repository)
                    );

                } catch (ModelNotFoundException $e) {
                    Log::warning("Repository not found for path: {$fullPath}");
                    $actions = [];
                }

                $intersectedScope = $requestedScope->setActions($actions);

                return response()->json([
                    'token' => Token::withScope($intersectedScope)
                        ->relatedTo($user->email)
                        ->expiresAt(now()->addMinutes(5))
                        ->issuedBy(config('dockhand.authority_name'))
                        ->permittedFor(config('dockhand.registry_name'))
                        ->toString(),
                ]);
        }
    }
}
