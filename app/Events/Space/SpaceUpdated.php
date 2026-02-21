<?php

namespace App\Events\Space;

use App\Contracts\TrackableEvent;
use App\Enums\Action;
use App\Models\Space;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SpaceUpdated implements TrackableEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Space $space,
        public ?User $actor = null
    ) {}

    public function action(): Action
    {
        return Action::SpaceUpdated;
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
        return [];
    }
}
