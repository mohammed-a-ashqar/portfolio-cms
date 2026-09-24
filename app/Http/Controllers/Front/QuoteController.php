<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Actions\Quotes\SubmitQuoteRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\Front\StoreQuoteRequestRequest;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

final class QuoteController extends Controller
{
    public function __construct(private readonly ServiceRepositoryInterface $services) {}

    public function create(): View
    {
        return view('front.quote', [
            'services' => $this->services->activeWithPackages(),
        ]);
    }

    public function store(StoreQuoteRequestRequest $request, SubmitQuoteRequest $action): RedirectResponse
    {
        $quote = $action->handle($request->toData());

        return redirect()
            ->route('quotes.create')
            ->with('status', __('quotes.form.success', ['reference' => $quote->reference]));
    }
}
