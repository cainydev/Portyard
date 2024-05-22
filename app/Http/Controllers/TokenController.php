<?php

namespace App\Http\Controllers;

use App\Facades\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Lcobucci\JWT\Token\Builder;

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

        if ($request->has('scope')) {
            Log::channel('stderr')->info('Found scope:');

            $scope = str($request->get('scope'))->explode(':');
            $resourceType = $scope[0];
            $resourceName = $scope[1];
            $actions = str($scope[2])->explode(',');

            Log::channel('stderr')
                ->info('Scope: ' . $resourceType . ':' . $resourceName . ':' . $actions->join(','));

            if ($resourceType == 'registry') {
                if ($resourceName == 'catalog') {
                    $token = JWT::createToken(function (Builder $builder) {
                        return $builder
                            ->issuedBy(config('registry.auth_name'))
                            ->permittedFor(config('registry.registry_name'))
                            ->relatedTo(auth()->user()->id)
                            ->withClaim('access', [
                                [
                                    'type' => 'registry',
                                    'name' => 'catalog',
                                    'actions' => ['*']
                                ]
                            ]);
                    });

                    return response()->json([
                        'token' => $token->toString(),
                    ]);
                }
            } else if ($resourceType == 'repository') {
                $allowedScopes = auth()->user()->intersectClaims($resourceName, $actions);

                $token = JWT::createToken(function (Builder $builder) use ($resourceName, $allowedScopes) {
                    return $builder
                        ->issuedBy(config('registry.auth_name'))
                        ->permittedFor(config('registry.registry_name'))
                        ->relatedTo(auth()->user()->id)
                        ->withClaim('access', [
                            [
                                'type' => 'repository',
                                'name' => $resourceName,
                                'actions' => $allowedScopes
                            ]
                        ]);
                });

                return response()->json([
                    'token' => $token->toString(),
                ]);
            }
        } else {
            Log::channel('stderr')->error('Didn\'t have scope to work with..');
        }
    }
}
