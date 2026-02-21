<?php

namespace App\Http\Middleware;

use App\Models\Space;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetSpaceDefaults
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. If guest, do nothing.
        if (! Auth::check()) {
            return $next($request);
        }

        $space = null;

        // 2. Try to resolve space from URL (String at this point, before binding)
        if ($slug = $request->route('space')) {
            $space = $slug instanceof Space
                ? $slug
                : Space::where('namespace', $slug)->first();
        }

        // 3. Fallback to Session (Global pages like /settings)
        if (! $space && $id = session('current_space_id')) {
            $space = Space::find($id);
        }

        // 4. Fallback to User Default (Fresh login)
        if (! $space) {
            $space = $request->user()->spaces()->first();
        }

        // 5. APPLY DEFAULTS & SYNC SESSION
        if ($space) {
            URL::defaults(['space' => $space]);

            $previousSpaceId = session('current_space_id');
            session(['current_space_id' => $space->id]);

            if ($previousSpaceId !== null && $previousSpaceId !== $space->id) {
                session()->flash('flux-toast', [
                    'heading' => 'Context Switched',
                    'text' => "You are now working in {$space->name}",
                    'variant' => 'success',
                ]);
            }
        }

        return $next($request);
    }
}
