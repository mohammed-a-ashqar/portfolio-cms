<?php

declare(strict_types=1);

namespace App\Actions\Projects;

use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Services\MediaService;
use Illuminate\Support\Facades\DB;

/**
 * Soft-deletes by default so an accidental delete is recoverable; `force`
 * also removes the stored files, which is the genuinely irreversible part.
 */
final readonly class DeleteProject
{
    public function __construct(
        private ProjectRepositoryInterface $projects,
        private MediaService $media,
    ) {}

    public function handle(Project $project, bool $force = false): bool
    {
        return DB::transaction(function () use ($project, $force): bool {
            if (! $force) {
                return $this->projects->delete($project);
            }

            $this->media->deletePath($project->cover_image);

            foreach ($project->images as $image) {
                $this->media->deletePath($image->path);
            }

            $project->technologies()->detach();
            $project->images()->delete();

            return (bool) $project->forceDelete();
        });
    }
}
