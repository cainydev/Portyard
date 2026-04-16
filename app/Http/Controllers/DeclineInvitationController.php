<?php

namespace App\Http\Controllers;

use App\Events\Space\MemberDeclined;
use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DeclineInvitationController extends Controller
{
    public function show(string $token): View
    {
        $invitation = $this->findInvitation($token);

        return view('pages.invitations.decline', [
            'invitation' => $invitation,
        ]);
    }

    public function destroy(string $token): RedirectResponse
    {
        $invitation = $this->findInvitation($token);

        $invitation->update(['declined_at' => now()]);

        MemberDeclined::dispatch($invitation->space, $invitation->email);

        return redirect()->route('root')
            ->with('info', __('You have declined the invitation.'));
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
