<?php

namespace App\Policies;

use App\Enums\Roles;
use App\Models\Repository;
use App\Models\User;

class RepositoryPolicy
{
    /**
     * View/Pull: Public OR Space Member (Any).
     */
    public function view(User $user, Repository $repository): bool
    {
        if ($repository->public) {
            return true;
        }

        return $repository->space->users->contains($user);
    }

    /**
     * Pull: Alias for view — used by Docker token scope requests.
     */
    public function pull(User $user, Repository $repository): bool
    {
        return $this->view($user, $repository);
    }

    /**
     * Push: Space (Dev+).
     */
    public function push(User $user, Repository $repository): bool
    {
        return $this->hasSpaceRole($user, $repository, [Roles::Owner, Roles::Maintainer, Roles::Developer]);
    }

    /**
     * Checks if the user has one of the given roles in the parent space.
     *
     * @param  array<\App\Enums\Roles|string>  $allowedRoles
     */
    protected function hasSpaceRole(User $user, Repository $repository, array $allowedRoles): bool
    {
        $allowedValues = array_map(fn ($r) => is_object($r) ? $r->value : $r, $allowedRoles);

        return $repository->space->users()
            ->where('user_id', $user->id)
            ->wherePivotIn('role', $allowedValues)
            ->exists();
    }

    /**
     * Manage Webhooks/Settings: Space (Maintainer+).
     */
    public function manageSettings(User $user, Repository $repository): bool
    {
        return $this->hasSpaceRole($user, $repository, [Roles::Owner, Roles::Maintainer]);
    }

    /**
     * Delete Repo: Space (Owner Only).
     */
    public function delete(User $user, Repository $repository): bool
    {
        return $this->hasSpaceRole($user, $repository, [Roles::Owner]);
    }
}
