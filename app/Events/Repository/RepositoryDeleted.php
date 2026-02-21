<?php

namespace App\Events\Repository;

use App\Contracts\TrackableEvent;
use App\Enums\Action;
use App\Models\Repository;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RepositoryDeleted implements TrackableEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Repository $repository,
        public User $actor
    ) {}

    public function action(): Action
    {
        return Action::RepositoryDeleted;
    }

    public function subject(): ?Model
    {
        return null;
    }

    public function spaceId(): string
    {
        return $this->repository->space_id;
    }

    public function properties(): array
    {
        return ['name' => $this->repository->name];
    }
}
