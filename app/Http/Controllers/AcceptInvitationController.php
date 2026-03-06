<?php

namespace App\Http\Controllers;

use App\Events\Space\MemberAccepted;
use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AcceptInvitationController extends Controller
{
    public function __invoke(Request $request, string $token): RedirectResponse
    {
        $invitation = Invitation::query()
            ->with('space')
            ->pending()
            ->where('token', $token)
            ->firstOrFail();

        if (! $request->user()) {
            return redirect()->guest(route('login'))
                ->with('url.intended', route('invitations.accept', $token));
        }

        if ($request->user()->email !== $invitation->email) {
            return redirect()->route('login')
                ->withErrors(['email' => __('You must log in with the email address this invitation was sent to.')]);
        }

        $invitation->space->users()->attach($request->user()->id, [
            'role' => $invitation->role->value,
        ]);

        $invitation->update(['accepted_at' => now()]);

        MemberAccepted::dispatch($invitation->space, $request->user());

        return redirect()->route('app.space.dashboard', $invitation->space)
            ->with('success', __('You have joined :space!', ['space' => $invitation->space->name]));
    }
}
