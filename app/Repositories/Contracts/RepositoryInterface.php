<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * The persistence contract every repository shares.
 *
 * Services depend on this, not on Eloquent, which is what makes them testable
 * with an in-memory double and keeps query logic out of controllers.
 *
 * @template TModel of Model
 */
interface RepositoryInterface
{
    /** @return TModel|null */
    public function find(int|string $id): ?Model;

    /** @return TModel */
    public function findOrFail(int|string $id): Model;

    /** @return TModel|null */
    public function findBySlug(string $slug): ?Model;

    /** @return Collection<int, TModel> */
    public function all(array $relations = []): Collection;

    /** @return LengthAwarePaginator<TModel> */
    public function paginate(int $perPage = 15, array $relations = []): LengthAwarePaginator;

    /** @return TModel */
    public function create(array $attributes): Model;

    /**
     * @param  TModel  $model
     * @return TModel
     */
    public function update(Model $model, array $attributes): Model;

    /** @param TModel $model */
    public function delete(Model $model): bool;

    public function count(): int;
}
