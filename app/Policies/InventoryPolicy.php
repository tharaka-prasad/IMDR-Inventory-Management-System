<?php

namespace App\Policies;

use App\Models\Inventory;
use App\Models\User;

class InventoryPolicy
{
    public function viewAny(User $user): bool
    {
        // Everyone can hit the index; the controller scopes assignees to
        // only the assets currently assigned to them.
        return true;
    }

    public function view(User $user, Inventory $inventory): bool
    {
        if ($user->isSuperAdmin() || $user->isAdmin()) {
            return true;
        }

        // Assignee: only view products currently/previously assigned to them.
        return $inventory->assignments()->where('assigned_to', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function update(User $user, Inventory $inventory): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function delete(User $user, Inventory $inventory): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }
}
