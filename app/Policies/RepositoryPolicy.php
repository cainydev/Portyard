<?php

namespace App\Policies;

use App\Models\Repository;
use App\Models\User;

class RepositoryPolicy
{
    /**
     * Determine whether the user can view the repo.
     */
    public function view(User $user, Repository $repository): bool
    {
        return $repository->public
            || $repository->users->contains($user);
    }

    /**
     * Determine whether the user can pull the repo.
     */
    public function pull(User $user, Repository $repository): bool
    {
        return $repository->public
            || $repository->users->contains($user);
    }

    /**
     * Determine whether the user can push models.
     */
    public function push(User $user, Repository $repository): bool
    {
        return $repository->developers->contains($user)
            || $repository->maintainers->contains($user)
            || $repository->owners->contains($user);
    }

    /**
     * Determine whether the user can edit the general settings.
     */
    public function editGeneral(User $user, Repository $repository): bool
    {
        return $repository->maintainers->contains($user)
            || $repository->owners->contains($user);
    }

    /**
     * Determine whether the user can edit the webhook settings.
     */
    public function editWebhooks(User $user, Repository $repository): bool
    {
        return $repository->maintainers->contains($user)
            || $repository->owners->contains($user);
    }

    /**
     * Determine whether the user can edit the permission settings.
     */
    public function editPermissions(User $user, Repository $repository): bool
    {
        return $repository->owners->contains($user);
    }

    /**
     * Determine whether the user can change visibility of the repo.
     */
    public function changeVisibility(User $user, Repository $repository): bool
    {
        return $repository->owners->contains($user);
    }

    /**
     * Determine whether the user can permanently delete the repo.
     */
    public function delete(User $user, Repository $repository): bool
    {
        return $repository->owners->contains($user);
    }
}
