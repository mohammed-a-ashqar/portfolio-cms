<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ServiceController extends Controller
{
    public function __construct(private readonly ServiceRepositoryInterface $services) {}

    public function index(): View
    {
        return view('front.services.index', [
            'services' => $this->services->activeWithPackages(),
        ]);
    }

    public function show(string $slug): View
    {
        $service = $this->services->findActiveBySlug($slug);

        if ($service === null) {
            throw new NotFoundHttpException;
        }

        return view('front.services.show', ['service' => $service]);
    }
}
