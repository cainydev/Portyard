<?php

namespace App\Http\Controllers;

use App\Events\Space\MemberAccepted;
use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AcceptInvitationController extends Controller
{
    public function show(Request $request, string $token): View|RedirectResponse
    {
        $invitation = $this->findInvitation($token);

        if (! $request->user()) {
            return redirect()->guest(route('login'))
                ->with('url.intended', route('invitations.accept.show', $token));
        }

        if ($request->user()->email !== $invitation->email) {
            return redirect()->route('login')
                ->withErrors(['email' => __('You must log in with the email address this invitation was sent to.')]);
        }

        return view('pages.invitations.accept', [
            'invitation' => $invitation,
        ]);
    }

    public function store(Request $request, string $token): RedirectResponse
    {
        $invitation = $this->findInvitation($token);

        if (! $request->user() || $request->user()->email !== $invitation->email) {
            abort(403);
        }

        DB::transaction(function () use ($invitation, $request) {
            $invitation->space->users()->syncWithoutDetaching([
                $request->user()->id => ['role' => $invitation->role->value],
            ]);

            $invitation->update(['accepted_at' => now()]);
        });

        MemberAccepted::dispatch($invitation->space, $request->user());

        return redirect()->route('app.space.dashboard', $invitation->space)
            ->with('success', __('You have joined :space!', ['space' => $invitation->space->name]));
    }

    private function findInvitation(string $token): Invitation
    {
        return Invitation::query()
            ->with('space')
            ->pending()
            ->where('token', $token)
            ->firstOrFail();
    }
}
