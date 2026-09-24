<?php

declare(strict_types=1);

namespace App\Support\Filters\Projects;

use App\Support\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

/**
 * Whitelisted sorting. Never interpolates the raw request value into SQL —
 * an unvalidated `orderBy` is a classic injection hole.
 */
final class SortFilter extends Filter
{
    private const ALLOWED = [
        'newest' => ['created_at', 'desc'],
        'oldest' => ['created_at', 'asc'],
        'popular' => ['views_count', 'desc'],
        'manual' => ['order_column', 'asc'],
    ];

    protected function shouldSkip(): bool
    {
        return false; // always applies, falling back to the default order
    }

    protected function apply(Builder $query): Builder
    {
        [$column, $direction] = self::ALLOWED[(string) $this->value] ?? self::ALLOWED['manual'];

        return $query->orderBy($column, $direction)->orderByDesc('id');
    }
}
