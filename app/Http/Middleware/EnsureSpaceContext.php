<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSpaceContext
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (session()->has('current_space_id')) {
            return $next($request);
        }

        $defaultSpace = $user->spaces()->first();

        if ($defaultSpace) {
            session(['current_space_id' => $defaultSpace->id]);
        }

        return $next($request);
    }
}
