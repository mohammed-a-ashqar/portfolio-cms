<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Support\Filters\FilterPipeline;
use App\Support\Filters\Projects\ProjectFilters;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * @extends BaseRepository<Project>
 */
final class ProjectRepository extends BaseRepository implements ProjectRepositoryInterface
{
    public function __construct(Project $model, private readonly FilterPipeline $pipeline)
    {
        parent::__construct($model);
    }

    public function filtered(ProjectFilters $filters, int $perPage = 12, bool $publishedOnly = false): LengthAwarePaginator
    {
        $query = $this->query()
            ->with(['category', 'technologies'])
            ->when($publishedOnly, fn (Builder $q) => $q->published());

        return $this->pipeline
            ->apply($query, $filters->toPipes())
            ->paginate($perPage)
            ->withQueryString();
    }

    public function featured(int $limit = 6): Collection
    {
        return $this->query()
            ->published()
            ->featured()
            ->with(['category', 'technologies'])
            ->ordered()
            ->limit($limit)
            ->get();
    }

    public function findPublishedBySlug(string $slug): ?Project
    {
        return $this->query()
            ->published()
            ->with(['category', 'technologies', 'images', 'testimonials', 'reels'])
            ->where('slug', $slug)
            ->first();
    }

    public function related(Project $project, int $limit = 3): Collection
    {
        return $this->query()
            ->published()
            ->whereKeyNot($project->getKey())
            ->when(
                $project->category_id,
                fn (Builder $q) => $q->where('category_id', $project->category_id),
                // No category? Fall back to sharing at least one technology.
                fn (Builder $q) => $q->whereHas(
                    'technologies',
                    fn (Builder $inner) => $inner->whereIn('technologies.id', $project->technologies->pluck('id')),
                ),
            )
            ->with(['category', 'technologies'])
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    public function countsByStatus(): array
    {
        $counts = $this->query()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->all();

        // Always return every status so the dashboard never renders a gap.
        return collect(ProjectStatus::cases())
            ->mapWithKeys(fn (ProjectStatus $status): array => [
                $status->value => (int) ($counts[$status->value] ?? 0),
            ])
            ->all();
    }

    public function syncTechnologies(Project $project, array $technologyIds): void
    {
        $project->technologies()->sync(array_filter($technologyIds));
    }
}
