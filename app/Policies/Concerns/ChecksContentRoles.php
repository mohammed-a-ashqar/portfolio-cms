<?php

declare(strict_types=1);

namespace App\Policies\Concerns;

use App\Enums\UserRole;
use App\Models\User;

/**
 * The rules shared by every content type: viewers read, editors write,
 * admins destroy. Because roles are ranked, each check is one comparison.
 */
trait ChecksContentRoles
{
    public function viewAny(User $user): bool
    {
        return $user->is_active && $user->hasRole(UserRole::Viewer);
    }

    public function create(User $user): bool
    {
        return $user->is_active && $user->hasRole(UserRole::Editor);
    }

    public function forceDelete(User $user): bool
    {
        return $user->is_active && $user->hasRole(UserRole::Admin);
    }
}
