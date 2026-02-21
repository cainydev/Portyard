<?php

namespace App\Policies;

use App\Enums\Roles;
use App\Models\Space;
use App\Models\User;

class SpacePolicy
{
    /**
     * Anyone in the space can view the dashboard.
     */
    public function view(User $user, Space $space): bool
    {
        return $user->spaces()->where('space_id', $space->id)->exists();
    }

    /**
     * Only Owners and Maintainers can update space settings (name, description).
     */
    public function update(User $user, Space $space): bool
    {
        return $this->hasSpaceRole($user, $space, [Roles::Owner, Roles::Maintainer]);
    }

    /**
     * Helper to check the pivot role.
     */
    protected function hasSpaceRole(User $user, Space $space, array $allowedRoles): bool
    {
        $allowedValues = array_map(fn ($r) => $r->value ?? $r, $allowedRoles);

        return $user->spaces()
            ->where('space_id', $space->id)
            ->wherePivotIn('role', $allowedValues)
            ->exists();
    }

    /**
     * Only Owners can delete the space.
     */
    public function delete(User $user, Space $space): bool
    {
        return $this->hasSpaceRole($user, $space, [Roles::Owner]);
    }

    /**
     * Only Owners (and maybe Maintainers) can invite new members to the space.
     */
    public function manageMembers(User $user, Space $space): bool
    {
        return $this->hasSpaceRole($user, $space, [Roles::Owner, Roles::Maintainer]);
    }

    /**
     * Only Owners and Maintainers can invite new members to the space.
     */
    public function invite(User $user, Space $space): bool
    {
        return $this->hasSpaceRole($user, $space, [Roles::Owner, Roles::Maintainer]);
    }

    /**
     * Anyone in the space can create a repository.
     */
    public function createRepository(User $user, Space $space): bool
    {
        return $this->hasSpaceRole($user, $space, [Roles::Owner, Roles::Maintainer, Roles::Developer]);
    }

    /**
     * Only Owners and Maintainers can delete a repository.
     */
    public function deleteRepository(User $user, Space $space): bool
    {
        return $this->hasSpaceRole($user, $space, [Roles::Owner, Roles::Maintainer]);
    }
}
