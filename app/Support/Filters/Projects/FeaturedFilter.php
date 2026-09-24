<?php

declare(strict_types=1);

namespace App\Support\Filters\Projects;

use App\Support\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

final class FeaturedFilter extends Filter
{
    protected function shouldSkip(): bool
    {
        return $this->value === null || $this->value === '';
    }

    protected function apply(Builder $query): Builder
    {
        return $query->where('is_featured', filter_var($this->value, FILTER_VALIDATE_BOOLEAN));
    }
}
