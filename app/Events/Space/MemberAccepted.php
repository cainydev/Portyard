<?php

namespace App\Events\Space;

use App\Contracts\TrackableEvent;
use App\Enums\Action;
use App\Models\Space;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MemberAccepted implements TrackableEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Space $space,
        public User $member
    ) {}

    public function action(): Action
    {
        return Action::MemberAccepted;
    }

    public function subject(): User
    {
        return $this->member;
    }

    public function spaceId(): string
    {
        return $this->space->id;
    }

    public function properties(): array
    {
        return ['email' => $this->member->email];
    }
}
