<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Reel;
use App\Models\User;
use App\Policies\Concerns\ChecksContentRoles;

final class ReelPolicy
{
    use ChecksContentRoles;

    public function view(User $user, Reel $reel): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Reel $reel): bool
    {
        return $user->is_active && $user->hasRole(UserRole::Editor);
    }

    public function delete(User $user, Reel $reel): bool
    {
        return $user->is_active && $user->hasRole(UserRole::Editor);
    }

    public function import(User $user): bool
    {
        return $this->create($user);
    }

    public function reorder(User $user): bool
    {
        return $this->create($user);
    }
}
