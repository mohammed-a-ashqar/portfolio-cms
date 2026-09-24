<?php

declare(strict_types=1);

namespace App\Support\Filters\Projects;

use App\Enums\ProjectStatus;
use App\Support\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

final class StatusFilter extends Filter
{
    protected function apply(Builder $query): Builder
    {
        $status = $this->value instanceof ProjectStatus
            ? $this->value
            : ProjectStatus::tryFrom((string) $this->value);

        return $status ? $query->where('status', $status) : $query;
    }
}
