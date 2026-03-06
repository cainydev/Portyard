<?php

namespace App\Http\Controllers;

use App\Models\Repository;
use App\Models\Space;
use App\Services\NamingService;
use Cainy\Dockhand\Enums\ScopeResourceType;
use Cainy\Dockhand\Facades\Scope;
use Cainy\Dockhand\Facades\Token;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use function array_filter;
use function config;
use function now;
use function response;

class TokenController extends Controller
{
    public function entry(Request $request): JsonResponse
    {
        $user = Auth::user();

        Log::info("New token request from {$user->name}!", $request->all());

        if (! $request->has('service') || $request->get('service') != config('dockhand.connections.default.auth.registry_name')) {
            Log::error('Request service didn\'t match registry name.');

            return $this->errorResponse('UNSUPPORTED', 'Invalid service.', 400);
        }

        if (! $request->has('scope')) {
            if ($request->has('account') && $request->get('account') !== $user->email) {
                Log::error('Account mismatch during login.');

                return $this->errorResponse('UNAUTHORIZED', 'Account mismatch.', 401);
            }

            return response()->json([
                'token' => Token::create()
                    ->relatedTo($user->email)
                    ->expiresAt(now()->addMinutes(5))
                    ->issuedBy(config('dockhand.connections.default.auth.authority_name'))
                    ->permittedFor(config('dockhand.connections.default.auth.registry_name'))
                    ->toString(),
            ]);
        }

        try {
            $requestedScope = Scope::fromString($request->get('scope'));
        } catch (Exception $e) {
            Log::error('Failed to parse scope: '.$e->getMessage());

            return $this->errorResponse('UNSUPPORTED', 'Invalid scope format.', 400);
        }

        switch ($requestedScope->getResourceType()) {
            case ScopeResourceType::Registry:
                return $this->errorResponse('UNAUTHORIZED', 'Registry-level scopes are not supported for standard users.', 401);

            case ScopeResourceType::Repository:
                $fullPath = $requestedScope->getResourceName();

                try {
                    $repository = Repository::fromPath($fullPath);

                    $actions = array_filter(
                        $requestedScope->getActions(),
                        fn (string $action) => $user->can($action, $repository)
                    );

                    if ($repository->space->isOverQuota()) {
                        $actions = array_filter($actions, fn (string $action) => $action !== 'push');
                        Log::warning("TokenController: Push denied — space '{$repository->space->namespace}' is over storage quota.");
                    }
                } catch (ModelNotFoundException $e) {
                    $actions = $this->resolveActionsForNewRepository($user, $fullPath, $requestedScope);
                }

                $intersectedScope = $requestedScope->setActions($actions);

                return response()->json([
                    'token' => Token::withScope($intersectedScope)
                        ->relatedTo($user->email)
                        ->expiresAt(now()->addMinutes(5))
                        ->issuedBy(config('dockhand.connections.default.auth.authority_name'))
                        ->permittedFor(config('dockhand.connections.default.auth.registry_name'))
                        ->toString(),
                ]);

            default:
                return $this->errorResponse('UNSUPPORTED', 'Unsupported resource type.', 400);
        }
    }

    /**
     * Grant actions for a repository that doesn't exist yet.
     *
     * Validates that the user could create the repo (valid name, space exists,
     * user has permission). The actual repo creation happens in ManifestPushedListener.
     *
     * @return array<string>
     */
    private function resolveActionsForNewRepository(mixed $user, string $fullPath, mixed $requestedScope): array
    {
        $parts = explode('/', $fullPath, 2);

        if (count($parts) !== 2) {
            return [];
        }

        [$namespace, $repoName] = $parts;

        if (! NamingService::isValidRepositoryName($repoName)) {
            return [];
        }

        $space = Space::where('namespace', $namespace)->first();

        if (! $space || ! $user->can('createRepository', $space)) {
            return [];
        }

        if ($space->isOverQuota()) {
            Log::warning("TokenController: Push denied — space '{$space->namespace}' is over storage quota.");

            return [];
        }

        Log::info("TokenController: Granting scope for new repository {$fullPath} (will be created on push).");

        return $requestedScope->getActions();
    }

    private function errorResponse(string $code, string $message, int $status): JsonResponse
    {
        return response()->json([
            'errors' => [['code' => $code, 'message' => $message]],
        ], $status);
    }
}
