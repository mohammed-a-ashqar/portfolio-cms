<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Enums\ReelProvider;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ReelRepositoryInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class ReelController extends Controller
{
    public function __construct(private readonly ReelRepositoryInterface $reels) {}

    public function index(Request $request): View
    {
        $provider = ReelProvider::tryFrom((string) $request->query('provider'));

        return view('front.reels.index', [
            'reels' => $this->reels->paginatedShowcase(
                (int) config('portfolio.per_page.reels'),
                $provider,
            ),
            'provider' => $provider,
            'counts' => $this->reels->countsByProvider(),
        ]);
    }
}
