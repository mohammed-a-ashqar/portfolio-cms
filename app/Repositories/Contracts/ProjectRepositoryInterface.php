<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Project;
use App\Support\Filters\Projects\ProjectFilters;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * @extends RepositoryInterface<Project>
 */
interface ProjectRepositoryInterface extends RepositoryInterface
{
    /** @return LengthAwarePaginator<Project> */
    public function filtered(ProjectFilters $filters, int $perPage = 12, bool $publishedOnly = false): LengthAwarePaginator;

    /** @return Collection<int, Project> */
    public function featured(int $limit = 6): Collection;

    public function findPublishedBySlug(string $slug): ?Project;

    /** @return Collection<int, Project> Other published projects in the same category. */
    public function related(Project $project, int $limit = 3): Collection;

    /** @return array<string, int> status => count, for the dashboard tiles. */
    public function countsByStatus(): array;

    public function syncTechnologies(Project $project, array $technologyIds): void;
}
