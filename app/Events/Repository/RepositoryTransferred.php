<?php

namespace App\Events\Repository;

use App\Contracts\TrackableEvent;
use App\Enums\Action;
use App\Models\Repository;
use App\Models\Space;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RepositoryTransferred implements TrackableEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Repository $repository,
        public Space $fromSpace,
        public Space $toSpace,
        public User $actor
    ) {}

    public function action(): Action
    {
        return Action::RepositoryTransferred;
    }

    public function subject(): Repository
    {
        return $this->repository;
    }

    public function spaceId(): string
    {
        return $this->toSpace->id;
    }

    public function properties(): array
    {
        return [
            'from_space_id' => $this->fromSpace->id,
            'from_space_name' => $this->fromSpace->name,
            'to_space_id' => $this->toSpace->id,
            'to_space_name' => $this->toSpace->name,
        ];
    }
}
