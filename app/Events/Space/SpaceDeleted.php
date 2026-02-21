<?php

namespace App\Events\Space;

use App\Contracts\TrackableEvent;
use App\Enums\Action;
use App\Models\Space;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SpaceDeleted implements TrackableEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Space $space,
        public ?User $actor = null
    ) {}

    public function action(): Action
    {
        return Action::SpaceDeleted;
    }

    public function subject(): ?Model
    {
        return null;
    }

    public function spaceId(): string
    {
        return $this->space->id;
    }

    public function properties(): array
    {
        return ['name' => $this->space->name, 'namespace' => $this->space->namespace];
    }
}
