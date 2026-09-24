<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Project;
use App\Models\User;
use App\Policies\Concerns\ChecksContentRoles;

final class ProjectPolicy
{
    use ChecksContentRoles;

    public function view(User $user, Project $project): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Project $project): bool
    {
        return $user->is_active && $user->hasRole(UserRole::Editor);
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->is_active && $user->hasRole(UserRole::Editor);
    }

    /** Restoring is an admin call — it can resurrect content that was pulled deliberately. */
    public function restore(User $user, Project $project): bool
    {
        return $user->is_active && $user->hasRole(UserRole::Admin);
    }

    public function publish(User $user, Project $project): bool
    {
        return $user->is_active && $user->hasRole(UserRole::Editor);
    }
}
