<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum UserRole: string
{
    use HasOptions;

    case Admin = 'admin';
    case Editor = 'editor';
    case Viewer = 'viewer';

    public function label(): string
    {
        return __("enums.user_role.{$this->value}");
    }

    /**
     * Roles higher in the list inherit everything below them, which keeps the
     * policies down to a single `atLeast` check instead of long or-chains.
     */
    public function level(): int
    {
        return match ($this) {
            self::Admin => 30,
            self::Editor => 20,
            self::Viewer => 10,
        };
    }

    public function atLeast(self $role): bool
    {
        return $this->level() >= $role->level();
    }
}
