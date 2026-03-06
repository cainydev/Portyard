<?php

use App\Http\Middleware\InitializeSpace;
use Cainy\Dockhand\Exceptions\ParseException;
use Cainy\Dockhand\Exceptions\UnauthorizedException;
use Cainy\Dockhand\Exceptions\UnknownException;
use Cainy\Dockhand\Exceptions\UnsupportedException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Middleware\SubstituteBindings;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('web', [
            InitializeSpace::class,
        ]);

        $middleware->prependToPriorityList(
            before: SubstituteBindings::class,
            prepend: InitializeSpace::class,
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (UnauthorizedException $e) {
            return response()->json([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => $e->getMessage()]],
            ], 401);
        });

        $exceptions->renderable(function (UnsupportedException $e) {
            return response()->json([
                'errors' => [['code' => 'UNSUPPORTED', 'message' => $e->getMessage()]],
            ], 400);
        });

        $exceptions->renderable(function (ParseException $e) {
            return response()->json([
                'errors' => [['code' => 'UNSUPPORTED', 'message' => $e->getMessage()]],
            ], 400);
        });

        $exceptions->renderable(function (UnknownException $e) {
            return response()->json([
                'errors' => [['code' => 'UNKNOWN', 'message' => $e->getMessage()]],
            ], 500);
        });
    })->create();
