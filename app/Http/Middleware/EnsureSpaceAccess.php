<?php

namespace App\Http\Middleware;

use App\Facades\CurrentSpace;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class EnsureSpaceAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $space = CurrentSpace::get();

        if (! $space || Gate::denies('view', $space)) {
            abort(404);
        }

        return $next($request);
    }
}
