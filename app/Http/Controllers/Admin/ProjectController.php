<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Projects\CreateProject;
use App\Actions\Projects\DeleteProject;
use App\Actions\Projects\UpdateProject;
use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectRequest;
use App\Http\Requests\Admin\UpdateProjectRequest;
use App\Models\Category;
use App\Models\Project;
use App\Models\Technology;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Support\Filters\Projects\ProjectFilters;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Thin by design: validation is in the FormRequest, persistence in the
 * Action, querying in the Repository. What is left is routing and redirects.
 */
final class ProjectController extends Controller
{
    public function __construct(private readonly ProjectRepositoryInterface $projects) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Project::class);

        return view('admin.projects.index', [
            'projects' => $this->projects->filtered(
                ProjectFilters::fromRequest($request),
                (int) config('portfolio.per_page.admin'),
            ),
            'filters' => ProjectFilters::fromRequest($request),
            'categories' => Category::query()->ordered()->get(),
            'statuses' => ProjectStatus::options(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Project::class);

        return view('admin.projects.form', [
            'project' => new Project(['status' => ProjectStatus::Draft]),
            'categories' => Category::query()->ordered()->get(),
            'technologies' => Technology::query()->ordered()->get(),
            'statuses' => ProjectStatus::options(),
        ]);
    }

    public function store(StoreProjectRequest $request, CreateProject $action): RedirectResponse
    {
        $project = $action->handle($request->toData());

        return redirect()
            ->route('admin.projects.edit', $project)
            ->with('status', __('projects.messages.created'));
    }

    public function show(Project $project): RedirectResponse
    {
        return redirect()->route('admin.projects.edit', $project);
    }

    public function edit(Project $project): View
    {
        $this->authorize('update', $project);

        return view('admin.projects.form', [
            'project' => $project->load(['technologies', 'images']),
            'categories' => Category::query()->ordered()->get(),
            'technologies' => Technology::query()->ordered()->get(),
            'statuses' => ProjectStatus::options(),
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project, UpdateProject $action): RedirectResponse
    {
        $action->handle($project, $request->toData());

        return redirect()
            ->route('admin.projects.edit', $project)
            ->with('status', __('projects.messages.updated'));
    }

    public function destroy(Project $project, DeleteProject $action): RedirectResponse
    {
        $this->authorize('delete', $project);

        $action->handle($project);

        return redirect()
            ->route('admin.projects.index')
            ->with('status', __('projects.messages.deleted'));
    }
}
