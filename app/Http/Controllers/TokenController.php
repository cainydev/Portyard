<?php

namespace App\Http\Controllers;

use App\Models\Repository;
use Cainy\Dockhand\Enums\ScopeResourceType;
use Cainy\Dockhand\Facades\Scope;
use Cainy\Dockhand\Facades\Token;
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

        if (!$request->has('service') || $request->get('service') != config('dockhand.registry_name')) {
            Log::error('Request service didn\'t match registry name.');
            abort(400);
        }

        if (!$request->has('scope')) {
            Log::info('Couldn\'t find a scope...');
            if ($request->has('account') && $request->get('account') == $user->email) {
                Log::info('Only logging in. Emails match.');
            } else if ($request->has('account')) {
                Log::error('Empty scope and provided account doesn\'t match user. Aborting.');
                abort(400);
            } else {
                Log::error('Empty scope and no account provided. Aborting.');
                abort(400);
            }

            $emptyToken = Token::create()
                ->relatedTo($user->email)
                ->expiresAt(now()->addMinutes(5))
                ->issuedBy(config('dockhand.authority_name'))
                ->permittedFor(config('dockhand.registry_name'))
                ->toString();

            Log::info('Generated token: ' . $emptyToken);

            return response()->json(['token' => $emptyToken]);
        }

        Log::info('Trying to parse the scope.');
        try {
            $requestedScope = Scope::fromString($request->get('scope'));
            Log::info('Parsed scope.');
            Log::info('Requested scope: ' . $requestedScope->toString());
        } catch (\Exception $e) {
            Log::error('Failed to parse scope: ' . $e->getMessage());
            abort(400, 'Invalid scope format.');
        }

        switch ($requestedScope->getResourceType()) {
            case ScopeResourceType::Registry:
                abort(401, 'We currently don\'t support registry tokens such as catalog for normal users.');
            case ScopeResourceType::Repository:
                $repoPath = $requestedScope->getResourceName();
                $actions = $requestedScope->getActions();
                $repository = Repository::where('path', $repoPath)->firstOrFail();

                $intersectedActions = array_filter($actions, fn(string $action) => $user->can($action, $repository));
                $intersectedScope = $requestedScope->setActions($intersectedActions);

                Log::info('Intersected scope: ' . $intersectedScope->toString());

                return response()->json([
                    'token' => Token::withScope($intersectedScope)
                        ->relatedTo($user->email)
                        ->expiresAt(now()->addMinutes(5))
                        ->issuedBy(config('dockhand.authority_name'))
                        ->permittedFor(config('dockhand.registry_name'))
                        ->toString()
                ]);
            default:
                Log::error('Invalid scope type: ' . $requestedScope);
                abort(400, 'Invalid scope type.');
        }
    }
}
