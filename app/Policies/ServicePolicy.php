<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Service;
use App\Models\User;
use App\Policies\Concerns\ChecksContentRoles;

final class ServicePolicy
{
    use ChecksContentRoles;

    public function view(User $user, Service $service): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Service $service): bool
    {
        return $user->is_active && $user->hasRole(UserRole::Editor);
    }

    public function delete(User $user, Service $service): bool
    {
        return $user->is_active && $user->hasRole(UserRole::Editor);
    }
}
