<?php

namespace App\Http\Middleware;

use App\Facades\CurrentSpace;
use App\Models\Space;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class InitializeSpace
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return $next($request);
        }

        $space = null;

        if ($slug = $request->route('space')) {
            // Since this runs before implicit bindings, $slug is a string
            $space = Space::where('namespace', $slug)->first();
        }

        if (! $space && $sessionId = session('current_space_id')) {
            $space = Space::find($sessionId);
        }

        if (! $space) {
            $space = $request->user()->spaces()->first();
        }

        if ($space) {
            CurrentSpace::set($space);
            URL::defaults(['space' => $space]);

            $previousSpaceId = session('current_space_id');

            if ($previousSpaceId !== $space->id) {
                session(['current_space_id' => $space->id]);

                if ($previousSpaceId !== null) {
                    session()->flash('flux-toast', [
                        'heading' => 'Context Switched',
                        'text' => "You are now working in {$space->name}",
                        'variant' => 'success',
                    ]);
                }
            }
        }

        return $next($request);
    }
}
