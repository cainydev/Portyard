<?php

use App\Http\Middleware\SetSpaceDefaults;
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
        $middleware->appendToGroup('web', SetSpaceDefaults::class);

        $middleware->prependToPriorityList(
            before: SubstituteBindings::class,
            prepend: SetSpaceDefaults::class,
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
