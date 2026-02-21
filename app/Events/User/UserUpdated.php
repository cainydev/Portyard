<?php

namespace App\Events\User;

use App\Contracts\TrackableEvent;
use App\Enums\Action;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserUpdated implements TrackableEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public User $user
    ) {}

    public function action(): Action
    {
        return Action::UserUpdated;
    }

    public function subject(): User
    {
        return $this->user;
    }

    public function spaceId(): ?string
    {
        return null;
    }

    public function properties(): array
    {
        return [];
    }
}
