<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\User;

class AssignmentPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // controller scopes assignees to their own rows
    }

    public function view(User $user, Assignment $assignment): bool
    {
        if ($user->isSuperAdmin() || $user->isAdmin()) {
            return true;
        }

        return $assignment->assigned_to === $user->id;
    }

    public function create(User $user): bool
    {
        // Issue Product: Super Admin + Admin only.
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function return(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function transfer(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }
}
