<?php

namespace App\Policies;

use App\Models\Maintenance;
use App\Models\User;

class MaintenancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function update(User $user, Maintenance $maintenance): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function delete(User $user, Maintenance $maintenance): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }
}
