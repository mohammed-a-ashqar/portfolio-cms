<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum MessageStatus: string
{
    use HasOptions;

    case Unread = 'unread';
    case Read = 'read';
    case Replied = 'replied';
    case Archived = 'archived';

    public function label(): string
    {
        return __("enums.message_status.{$this->value}");
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Unread => 'bg-blue-50 text-blue-700 ring-blue-600/20',
            self::Read => 'bg-slate-100 text-slate-700 ring-slate-600/20',
            self::Replied => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
            self::Archived => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        };
    }
}
