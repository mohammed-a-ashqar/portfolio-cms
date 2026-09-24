<?php

declare(strict_types=1);

namespace App\Support\Filters\Projects;

use App\Support\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

final class CategoryFilter extends Filter
{
    protected function apply(Builder $query): Builder
    {
        return $query->whereHas('category', fn (Builder $q) => $q->where('slug', $this->value));
    }
}
