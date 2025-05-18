<?php

namespace App\Http\Controllers;

use Cainy\Dockhand\Facades\Scope;
use Cainy\Dockhand\Facades\Token;
use Cainy\Dockhand\Resources\ScopeResourceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use function abort;
use function config;
use function response;

class TokenController extends Controller
{
    public function entry(Request $request)
    {
        Log::channel('stderr')->info('New token request!');
        Log::channel('stderr')->info($request->all());

        if ($request->get('service') != config('registry.registry_name')) {
            Log::channel('stderr')->error('Request service didn\'t match registry name');
            abort(400);
        }

        if ($request->has('account') && !$request->has('scope')) {
            Log::channel('stderr')->info('Only logging in.');
            return response()->json([
                'token' => $request->attributes->get('catalog_token'),
            ]);
        }

        if (!$request->has('scope')) {
            Log::channel('stderr')->error('No scope provided.');
        }

        $requestedScope = Scope::fromString($request->get('scope'));
        Log::channel('stderr')->info('Requested scope: ' . $requestedScope->toString());

        switch ($requestedScope->getResourceType()) {
            case ScopeResourceType::Registry:
                abort(401, 'We currently don\'t support registry tokens such as catalog for normal users.');
            case ScopeResourceType::Repository:
                $repositoryName = $requestedScope->getResourceName();
                $actions = $requestedScope->getActions();
                break;

            // TODO: Check if user has access to repository
        }

        return response()->json([
            'token' => Token::withScope($requestedScope)->toString(),
        ]);
    }
}
