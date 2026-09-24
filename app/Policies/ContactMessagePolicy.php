<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\ContactMessage;
use App\Models\User;
use App\Policies\Concerns\ChecksContentRoles;

final class ContactMessagePolicy
{
    use ChecksContentRoles;

    public function view(User $user, ContactMessage $message): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, ContactMessage $message): bool
    {
        return $user->is_active && $user->hasRole(UserRole::Editor);
    }

    public function delete(User $user, ContactMessage $message): bool
    {
        return $user->is_active && $user->hasRole(UserRole::Admin);
    }
}
