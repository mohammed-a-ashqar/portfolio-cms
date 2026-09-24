<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum ProjectStatus: string
{
    use HasOptions;

    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';

    public function label(): string
    {
        return __("enums.project_status.{$this->value}");
    }

    /** Tailwind badge classes, kept next to the case so the UI never guesses. */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Draft => 'bg-slate-100 text-slate-700 ring-slate-600/20',
            self::Published => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
            self::Archived => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        };
    }

    public function isVisibleToPublic(): bool
    {
        return $this === self::Published;
    }
}
