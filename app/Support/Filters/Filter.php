<?php

declare(strict_types=1);

namespace App\Support\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;

/**
 * One filter = one pipe in a Laravel pipeline.
 *
 * Each subclass owns a single clause, so the listing query grows by adding a
 * class instead of another `if ($request->has(...))` in a controller.
 */
abstract class Filter
{
    public function __construct(protected mixed $value = null) {}

    public function handle(Builder $query, Closure $next): Builder
    {
        if ($this->shouldSkip()) {
            return $next($query);
        }

        return $next($this->apply($query));
    }

    protected function shouldSkip(): bool
    {
        return blank($this->value);
    }

    abstract protected function apply(Builder $query): Builder;
}
