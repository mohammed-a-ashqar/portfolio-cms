<?php

declare(strict_types=1);

namespace App\Support\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Manual drag-and-drop ordering backed by an integer column.
 *
 * New records land at the end automatically, so the admin never has to think
 * about the column and reordering is a single bulk statement.
 */
trait Sortable
{
    public static function bootSortable(): void
    {
        static::creating(function (Model $model): void {
            if ($model->{$model->sortColumn()} === null) {
                $model->{$model->sortColumn()} = $model->nextSortPosition();
            }
        });
    }

    public function sortColumn(): string
    {
        return 'order_column';
    }

    public function nextSortPosition(): int
    {
        return (int) static::query()->max($this->sortColumn()) + 1;
    }

    public function scopeOrdered(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderBy($this->sortColumn(), $direction)->orderBy($this->getKeyName(), $direction);
    }

    /**
     * Persist a new order in one round trip.
     *
     * @param  array<int, int|string>  $orderedIds
     */
    public static function applyOrder(array $orderedIds): void
    {
        $instance = new static;
        $column = $instance->sortColumn();

        collect($orderedIds)
            ->values()
            ->each(fn ($id, int $index) => static::query()->whereKey($id)->update([$column => $index + 1]));
    }
}
