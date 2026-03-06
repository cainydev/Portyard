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
