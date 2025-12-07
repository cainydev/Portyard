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
        return $space->users->contains($user);
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
        $member = $space->users()->where('user_id', $user->id)->first();

        $allowedValues = array_map(fn ($r) => $r->value, $allowedRoles);

        return $member && in_array($member->pivot->role, $allowedValues);
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
}
