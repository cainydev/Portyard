<?php

namespace App\Policies;

use App\Models\Repository;
use App\Models\User;
use App\Models\Webhook;

class WebhookPolicy
{
    public function viewAny(User $user, Repository $repository): bool
    {
        return $this->delegateToRepository($user, $repository);
    }

    public function view(User $user, Webhook $webhook): bool
    {
        return $this->delegateToRepository($user, $webhook->repository);
    }

    public function create(User $user, Repository $repository): bool
    {
        return $this->delegateToRepository($user, $repository);
    }

    public function update(User $user, Webhook $webhook): bool
    {
        return $this->delegateToRepository($user, $webhook->repository);
    }

    public function delete(User $user, Webhook $webhook): bool
    {
        return $this->delegateToRepository($user, $webhook->repository);
    }

    protected function delegateToRepository(User $user, Repository $repository): bool
    {
        return (new RepositoryPolicy)->manageSettings($user, $repository);
    }
}
