<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\PortfolioService;
use Illuminate\Contracts\View\View;

final class HomeController extends Controller
{
    public function __construct(private readonly PortfolioService $portfolio) {}

    public function index(): View
    {
        return view('front.home', [
            ...$this->portfolio->homePage(),
            'stats' => $this->portfolio->headlineStats(),
        ]);
    }
}
