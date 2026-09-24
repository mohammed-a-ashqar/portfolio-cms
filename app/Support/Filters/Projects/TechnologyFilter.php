<?php

declare(strict_types=1);

namespace App\Support\Filters\Projects;

use App\Support\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

final class TechnologyFilter extends Filter
{
    protected function apply(Builder $query): Builder
    {
        $slugs = is_array($this->value) ? $this->value : [$this->value];

        return $query->whereHas('technologies', fn (Builder $q) => $q->whereIn('slug', $slugs));
    }
}
