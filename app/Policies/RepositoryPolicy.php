<?php

namespace App\Policies;

use App\Enums\Roles;
use App\Models\Repository;
use App\Models\User;

class RepositoryPolicy
{
    /**
     * View/Pull: Public OR Space Member (Any) OR Repo Collaborator (Any).
     */
    public function view(User $user, Repository $repository): bool
    {
        if ($repository->public) {
            return true;
        }

        if ($repository->space->users->contains($user)) {
            return true;
        }

        return $repository->users->contains($user);
    }

    /**
     * Push: Space (Dev+) OR Repo (Dev+).
     */
    public function push(User $user, Repository $repository): bool
    {
        $writeRoles = [Roles::Owner, Roles::Maintainer, Roles::Developer];

        if ($this->hasSpaceRole($user, $repository, $writeRoles)) {
            return true;
        }

        return $this->hasRepoRole($user, $repository, $writeRoles);
    }

    /**
     * Checks if the user has a specific role in the parent space.
     */
    protected function hasSpaceRole(User $user, Repository $repository, array $allowedRoles): bool
    {
        $membership = $repository->space->users()
            ->where('user_id', $user->id)
            ->first();

        if (! $membership) {
            return false;
        }

        $allowedValues = array_map(fn ($r) => $r->value, $allowedRoles);

        return in_array($membership->pivot->role, $allowedValues);
    }

    /**
     * Checks if the user has a specific role explicitly on the REPOSITORY.
     */
    protected function hasRepoRole(User $user, Repository $repository, array $allowedRoles): bool
    {
        $collaborator = $repository->users()
            ->where('user_id', $user->id)
            ->first();

        if (! $collaborator) {
            return false;
        }

        $allowedValues = array_map(fn ($r) => $r->value, $allowedRoles);

        return in_array($collaborator->pivot->role, $allowedValues);
    }

    /**
     * Manage Webhooks/Settings: Space (Maintainer+) OR Repo (Maintainer+).
     */
    public function manageSettings(User $user, Repository $repository): bool
    {
        $adminRoles = [Roles::Owner, Roles::Maintainer];

        if ($this->hasSpaceRole($user, $repository, $adminRoles)) {
            return true;
        }

        return $this->hasRepoRole($user, $repository, $adminRoles);
    }

    /**
     * Delete Repo: Space (Owner Only) OR Repo (Owner Only).
     */
    public function delete(User $user, Repository $repository): bool
    {
        if ($this->hasSpaceRole($user, $repository, [Roles::Owner])) {
            return true;
        }

        return $this->hasRepoRole($user, $repository, [Roles::Owner]);
    }
}
