<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum QuoteStatus: string
{
    use HasOptions;

    case New = 'new';
    case InReview = 'in_review';
    case Quoted = 'quoted';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Closed = 'closed';

    public function label(): string
    {
        return __("enums.quote_status.{$this->value}");
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::New => 'bg-blue-50 text-blue-700 ring-blue-600/20',
            self::InReview => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20',
            self::Quoted => 'bg-violet-50 text-violet-700 ring-violet-600/20',
            self::Accepted => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
            self::Rejected => 'bg-rose-50 text-rose-700 ring-rose-600/20',
            self::Closed => 'bg-slate-100 text-slate-700 ring-slate-600/20',
        };
    }

    /**
     * The state machine. Encoding allowed moves here stops controllers from
     * inventing their own rules and makes the workflow testable in isolation.
     *
     * @return array<int, self>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::New => [self::InReview, self::Rejected, self::Closed],
            self::InReview => [self::Quoted, self::Rejected, self::Closed],
            self::Quoted => [self::Accepted, self::Rejected, self::Closed],
            self::Accepted, self::Rejected => [self::Closed],
            self::Closed => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions(), true);
    }

    public function isOpen(): bool
    {
        return $this->isNot(self::Accepted, self::Rejected, self::Closed);
    }
}
