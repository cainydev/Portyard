<?php

namespace App\Events\Space;

use App\Contracts\TrackableEvent;
use App\Enums\Action;
use App\Models\Space;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MemberInvited implements TrackableEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Space $space,
        public string $email,
        public string $role,
        public User $actor
    ) {}

    public function action(): Action
    {
        return Action::MemberInvited;
    }

    public function subject(): Space
    {
        return $this->space;
    }

    public function spaceId(): string
    {
        return $this->space->id;
    }

    public function properties(): array
    {
        return ['email' => $this->email, 'role' => $this->role];
    }
}
