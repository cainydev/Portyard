<?php

namespace App\Http\Middleware;

use App\Models\Space;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class EnsureSpaceAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $targetSpace = $request->route('space');

        if (! $targetSpace instanceof Space) {

            return $next($request);
        }

        if (Gate::denies('view', $targetSpace)) {
            abort(404);
        }

        $currentId = session('current_space_id');

        if ($currentId !== $targetSpace->id) {
            session(['current_space_id' => $targetSpace->id]);
        }

        return $next($request);
    }
}
