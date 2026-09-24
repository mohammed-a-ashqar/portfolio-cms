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

final readonly class UpdateProject
{
    public function __construct(
        private ProjectRepositoryInterface $projects,
        private MediaService $media,
    ) {}

    public function handle(Project $project, ProjectData $data): Project
    {
        $wasPublished = $project->isPublished();

        $updated = DB::transaction(function () use ($project, $data): Project {
            $attributes = $data->toAttributes();

            if ($data->hasCoverImage()) {
                // Replace, then remove the old file — in that order, so a
                // failed upload never leaves the project with no cover.
                $previous = $project->cover_image;
                $attributes['cover_image'] = $this->media->storePath($data->coverImage, 'projects');
                $this->media->deletePath($previous);
            }

            $updated = $this->projects->update($project, $attributes);

            $this->projects->syncTechnologies($updated, $data->technologyIds);
            $this->appendGallery($updated, $data);

            return $updated;
        });

        if (! $wasPublished && $updated->isPublished()) {
            ProjectPublished::dispatch($updated);
        }

        return $updated->load(['category', 'technologies', 'images']);
    }

    private function appendGallery(Project $project, ProjectData $data): void
    {
        $offset = (int) $project->images()->max('order_column');

        foreach ($data->galleryImages as $index => $image) {
            ProjectImage::create([
                'project_id' => $project->getKey(),
                'path' => $this->media->storePath($image, 'projects/gallery'),
                'order_column' => $offset + $index + 1,
            ]);
        }
    }
}
