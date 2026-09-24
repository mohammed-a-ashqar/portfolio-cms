<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Quotes\TransitionQuoteStatus;
use App\Enums\QuoteStatus;
use App\Http\Controllers\Controller;
use App\Models\QuoteRequest;
use App\Repositories\Contracts\QuoteRequestRepositoryInterface;
use App\Support\Pricing\Money;
use DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

final class QuoteRequestController extends Controller
{
    public function __construct(private readonly QuoteRequestRepositoryInterface $quotes) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', QuoteRequest::class);

        return view('admin.quotes.index', [
            'quotes' => $this->quotes->listing(
                QuoteStatus::tryFrom((string) $request->query('status')),
                $request->string('search')->trim()->value() ?: null,
                (int) config('portfolio.per_page.admin'),
            ),
            'counts' => $this->quotes->countsByStatus(),
            'statuses' => QuoteStatus::options(),
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    public function show(QuoteRequest $quote): View
    {
        $this->authorize('view', $quote);

        return view('admin.quotes.show', [
            'quote' => $quote->load(['service', 'package']),
            // Only offer the moves the state machine actually allows.
            'transitions' => $quote->status->allowedTransitions(),
        ]);
    }

    public function transition(Request $request, QuoteRequest $quote, TransitionQuoteStatus $action): RedirectResponse
    {
        $this->authorize('transition', $quote);

        $validated = $request->validate([
            'status' => ['required', new Enum(QuoteStatus::class)],
            'quoted_amount' => ['nullable', 'numeric', 'min:0'],
            'admin_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $target = QuoteStatus::from($validated['status']);

        try {
            $action->handle(
                $quote,
                $target,
                isset($validated['quoted_amount'])
                    ? Money::fromMajorUnits($validated['quoted_amount'], $quote->currency)
                    : null,
                $validated['admin_notes'] ?? null,
            );
        } catch (DomainException $exception) {
            return back()->withErrors(['status' => $exception->getMessage()]);
        }

        return back()->with('status', __('quotes.messages.status_updated', ['status' => $target->label()]));
    }

    public function destroy(QuoteRequest $quote): RedirectResponse
    {
        $this->authorize('delete', $quote);

        $quote->delete();

        return redirect()
            ->route('admin.quotes.index')
            ->with('status', __('quotes.messages.deleted'));
    }
}
