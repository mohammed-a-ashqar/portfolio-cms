<?php

declare(strict_types=1);

namespace App\Support\Filters;

use App\Support\Filters\Projects\ProjectFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pipeline\Pipeline;

/**
 * Runs a query through an ordered list of filters.
 *
 * @see ProjectFilters for how a caller builds the list.
 */
final readonly class FilterPipeline
{
    public function __construct(private Pipeline $pipeline) {}

    /** @param array<int, Filter> $filters */
    public function apply(Builder $query, array $filters): Builder
    {
        return $this->pipeline
            ->send($query)
            ->through($filters)
            ->thenReturn();
    }
}
