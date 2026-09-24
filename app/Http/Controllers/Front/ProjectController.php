<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Project;
use App\Models\Technology;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Support\Filters\Projects\ProjectFilters;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ProjectController extends Controller
{
    public function __construct(private readonly ProjectRepositoryInterface $projects) {}

    public function index(Request $request): View
    {
        $filters = ProjectFilters::fromRequest($request);

        return view('front.projects.index', [
            'projects' => $this->projects->filtered(
                $filters,
                (int) config('portfolio.per_page.projects'),
                publishedOnly: true,
            ),
            'categories' => Category::query()->active()->ordered()->get(),
            'technologies' => Technology::query()->ordered()->get(),
            'filters' => $filters,
        ]);
    }

    public function show(string $slug): View
    {
        // Resolved through the repository rather than route-model binding so
        // an unpublished project 404s instead of leaking a draft.
        $project = $this->projects->findPublishedBySlug($slug);

        if ($project === null) {
            throw new NotFoundHttpException;
        }

        $project->recordView();

        return view('front.projects.show', [
            'project' => $project,
            'related' => $this->projects->related($project),
        ]);
    }
}
