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
     * Determine whether the user can delete from the repo.
     */
    public function delete(User $user, Repository $repository): bool
    {
        return $repository->developers->contains($user)
            || $repository->maintainers->contains($user)
            || $repository->owners->contains($user);
    }

    /**
     * Determine whether the user can edit/view the general settings.
     */
    public function manageGeneral(User $user, Repository $repository): bool
    {
        return $repository->maintainers->contains($user)
            || $repository->owners->contains($user);
    }

    /**
     * Determine whether the user can edit/view the webhook settings.
     */
    public function manageWebhooks(User $user, Repository $repository): bool
    {
        return $repository->maintainers->contains($user)
            || $repository->owners->contains($user);
    }

    /**
     * Determine whether the user can edit/view the permission settings.
     */
    public function manageCollaborators(User $user, Repository $repository): bool
    {
        return $repository->owners->contains($user);
    }

    /**
     * Determine whether the user can change visibility of the repo.
     */
    public function changeVisibility(User $user, Repository $repository): bool
    {
        return $this->manageSettings($user, $repository);
    }

    /**
     * Determine whether the user can edit/view the settings;
     */
    public function manageSettings(User $user, Repository $repository): bool
    {
        return $repository->owners->contains($user);
    }

    /**
     * Determine whether the user can permanently delete the repo.
     */
    public function deleteRepository(User $user, Repository $repository): bool
    {
        return $this->manageSettings($user, $repository);
    }
}
