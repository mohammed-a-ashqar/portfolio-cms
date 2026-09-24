<?php

declare(strict_types=1);

namespace App\Actions\Projects;

use App\DTOs\ProjectData;
use App\Events\ProjectPublished;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Services\MediaService;
use Illuminate\Support\Facades\DB;

/**
 * Creates a project with its cover, gallery and technology links.
 *
 * One public method, one job. Wrapped in a transaction so a failed gallery
 * upload cannot leave a half-built project behind.
 */
final readonly class CreateProject
{
    public function __construct(
        private ProjectRepositoryInterface $projects,
        private MediaService $media,
    ) {}

    public function handle(ProjectData $data): Project
    {
        $project = DB::transaction(function () use ($data): Project {
            $attributes = $data->toAttributes();

            if ($data->hasCoverImage()) {
                $attributes['cover_image'] = $this->media->storePath($data->coverImage, 'projects');
            }

            $project = $this->projects->create($attributes);

            $this->projects->syncTechnologies($project, $data->technologyIds);
            $this->attachGallery($project, $data);

            return $project;
        });

        // Fired outside the transaction: listeners must never see a row that
        // a later rollback removes.
        if ($project->isPublished()) {
            ProjectPublished::dispatch($project);
        }

        return $project->load(['category', 'technologies', 'images']);
    }

    private function attachGallery(Project $project, ProjectData $data): void
    {
        foreach ($data->galleryImages as $index => $image) {
            ProjectImage::create([
                'project_id' => $project->getKey(),
                'path' => $this->media->storePath($image, 'projects/gallery'),
                'order_column' => $index + 1,
            ]);
        }
    }
}
