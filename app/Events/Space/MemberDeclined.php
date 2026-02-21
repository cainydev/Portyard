<?php

namespace App\Events\Space;

use App\Contracts\TrackableEvent;
use App\Enums\Action;
use App\Models\Space;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MemberDeclined implements TrackableEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Space $space,
        public string $email
    ) {}

    public function action(): Action
    {
        return Action::MemberDeclined;
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
        return ['email' => $this->email];
    }
}
