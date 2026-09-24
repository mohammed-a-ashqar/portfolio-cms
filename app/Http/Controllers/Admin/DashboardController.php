<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ContactMessageRepositoryInterface;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Repositories\Contracts\QuoteRequestRepositoryInterface;
use App\Repositories\Contracts\ReelRepositoryInterface;
use Illuminate\Contracts\View\View;

/**
 * Single-action controller: the dashboard has one job and no CRUD siblings.
 */
final class DashboardController extends Controller
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projects,
        private readonly QuoteRequestRepositoryInterface $quotes,
        private readonly ContactMessageRepositoryInterface $messages,
        private readonly ReelRepositoryInterface $reels,
    ) {}

    public function __invoke(): View
    {
        $projectCounts = $this->projects->countsByStatus();

        return view('admin.dashboard', [
            'projectCounts' => $projectCounts,
            'totalProjects' => array_sum($projectCounts),
            'openQuotes' => $this->quotes->openCount(),
            'quoteCounts' => $this->quotes->countsByStatus(),
            'quotesTrend' => $this->quotes->monthlyTotals(6),
            'unreadMessages' => $this->messages->unreadCount(),
            'reelCount' => $this->reels->count(),
            'recentQuotes' => $this->quotes->listing(perPage: 5),
            'recentMessages' => $this->messages->listing(perPage: 5),
        ]);
    }
}
