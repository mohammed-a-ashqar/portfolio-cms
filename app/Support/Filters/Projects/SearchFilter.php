<?php

declare(strict_types=1);

namespace App\Support\Filters\Projects;

use App\Support\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

/** Free-text search across the active locale's title/summary and the client name. */
final class SearchFilter extends Filter
{
    protected function apply(Builder $query): Builder
    {
        $term = trim((string) $this->value);
        $locale = app()->getLocale();

        return $query->where(function (Builder $q) use ($term, $locale): void {
            $q->where("title->{$locale}", 'like', "%{$term}%")
                ->orWhere("summary->{$locale}", 'like', "%{$term}%")
                ->orWhere('client_name', 'like', "%{$term}%")
                ->orWhere('slug', 'like', "%{$term}%");
        });
    }
}
