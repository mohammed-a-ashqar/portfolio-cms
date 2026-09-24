<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\QuoteRequest;
use App\Models\User;
use App\Policies\Concerns\ChecksContentRoles;

final class QuoteRequestPolicy
{
    use ChecksContentRoles;

    public function view(User $user, QuoteRequest $quote): bool
    {
        return $this->viewAny($user);
    }

    /** Quotes are never created from the admin — only by visitors. */
    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, QuoteRequest $quote): bool
    {
        return $user->is_active && $user->hasRole(UserRole::Editor);
    }

    public function transition(User $user, QuoteRequest $quote): bool
    {
        return $this->update($user, $quote);
    }

    public function delete(User $user, QuoteRequest $quote): bool
    {
        return $user->is_active && $user->hasRole(UserRole::Admin);
    }
}
