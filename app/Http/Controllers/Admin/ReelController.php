<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Reels\ImportReel;
use App\Actions\Reels\ReorderReels;
use App\Enums\ReelProvider;
use App\Exceptions\ReelImportException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImportReelRequest;
use App\Http\Requests\Admin\UpdateReelRequest;
use App\Models\Project;
use App\Models\Reel;
use App\Repositories\Contracts\ReelRepositoryInterface;
use App\Services\MediaService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class ReelController extends Controller
{
    public function __construct(private readonly ReelRepositoryInterface $reels) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Reel::class);

        return view('admin.reels.index', [
            'reels' => $this->reels->paginatedShowcase(
                (int) config('portfolio.per_page.admin'),
                ReelProvider::tryFrom((string) $request->query('provider')),
            ),
            'counts' => $this->reels->countsByProvider(),
            'provider' => $request->query('provider'),
        ]);
    }

    public function create(): View
    {
        $this->authorize('import', Reel::class);

        return view('admin.reels.form', [
            'reel' => new Reel(['is_active' => true]),
            'projects' => Project::query()->ordered()->get(),
        ]);
    }

    public function store(ImportReelRequest $request, ImportReel $action): RedirectResponse
    {
        try {
            $reel = $action->handle($request->toData());
        } catch (ReelImportException $exception) {
            return back()->withInput()->withErrors(['url' => $exception->getMessage()]);
        }

        return redirect()
            ->route('admin.reels.edit', $reel)
            ->with('status', __('reels.messages.imported'));
    }

    public function edit(Reel $reel): View
    {
        $this->authorize('update', $reel);

        return view('admin.reels.form', [
            'reel' => $reel,
            'projects' => Project::query()->ordered()->get(),
        ]);
    }

    public function update(UpdateReelRequest $request, Reel $reel, ImportReel $action): RedirectResponse
    {
        try {
            // Re-running the import keeps the record in sync with the platform
            // and is safe: the (provider, external_id) unique key makes it an upsert.
            $action->handle($request->toData());
        } catch (ReelImportException $exception) {
            return back()->withInput()->withErrors(['url' => $exception->getMessage()]);
        }

        return redirect()
            ->route('admin.reels.index')
            ->with('status', __('reels.messages.updated'));
    }

    public function destroy(Reel $reel, MediaService $media): RedirectResponse
    {
        $this->authorize('delete', $reel);

        $media->deletePath($reel->thumbnail_path);
        $reel->delete();

        return redirect()
            ->route('admin.reels.index')
            ->with('status', __('reels.messages.deleted'));
    }

    /** Called by the drag-and-drop grid; responds to fetch(), not a form post. */
    public function reorder(Request $request, ReorderReels $action): JsonResponse
    {
        $this->authorize('reorder', Reel::class);

        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:reels,id'],
        ]);

        $action->handle($validated['ids']);

        return response()->json(['message' => __('reels.actions.reorder_saved')]);
    }
}
