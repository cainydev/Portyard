<?php

namespace App\Http\Controllers;

use App\Events\Space\MemberDeclined;
use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;

class DeclineInvitationController extends Controller
{
    public function __invoke(string $token): RedirectResponse
    {
        $invitation = Invitation::query()
            ->with('space')
            ->pending()
            ->where('token', $token)
            ->firstOrFail();

        $invitation->update(['declined_at' => now()]);

        MemberDeclined::dispatch($invitation->space, $invitation->email);

        return redirect()->route('root')
            ->with('info', __('You have declined the invitation.'));
    }
}
