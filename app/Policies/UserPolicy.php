<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function view(User $user, User $target): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin() || $user->id === $target->id;
    }

    /**
     * General "create a user" check. Which ROLE they may create is enforced
     * separately via createRole(), since Admins may create Assignees only
     * while Super Admins may create Admins and Assignees.
     */
    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function createRole(User $user, string $roleToCreate): bool
    {
        if ($user->isSuperAdmin()) {
            // Super Admin can create Admins and Assignees (not another Super Admin
            // via this flow, to keep that a deliberate, rare action).
            return in_array($roleToCreate, ['admin', 'assignee'], true);
        }

        if ($user->isAdmin()) {
            // Admin can create Assignees only.
            return $roleToCreate === 'assignee';
        }

        return false;
    }

    public function update(User $user, User $target): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isAdmin()) {
            // Admin may only manage Assignees, never other Admins or Super Admins.
            return $target->isAssignee();
        }

        return false;
    }

    public function delete(User $user, User $target): bool
    {
        return $this->update($user, $target) && $user->id !== $target->id;
    }

    public function resetPassword(User $user, User $target): bool
    {
        return $this->update($user, $target);
    }

    public function changeRole(User $user, User $target): bool
    {
        // Only Super Admin may change a user's role (Doc 1: "Change Role" is
        // an Admin Management action, gated to Super Admin).
        return $user->isSuperAdmin();
    }
}
