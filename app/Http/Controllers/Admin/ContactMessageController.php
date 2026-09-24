<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\MessageStatus;
use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Repositories\Contracts\ContactMessageRepositoryInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class ContactMessageController extends Controller
{
    public function __construct(private readonly ContactMessageRepositoryInterface $messages) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', ContactMessage::class);

        return view('admin.messages.index', [
            'messages' => $this->messages->listing(
                MessageStatus::tryFrom((string) $request->query('status')),
                $request->string('search')->trim()->value() ?: null,
                (int) config('portfolio.per_page.admin'),
            ),
            'statuses' => MessageStatus::options(),
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    public function show(ContactMessage $message): View
    {
        $this->authorize('view', $message);

        // Opening a message is what marks it read - no extra click needed.
        $message->markAsRead();

        return view('admin.messages.show', ['message' => $message]);
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $this->authorize('delete', $message);

        $message->delete();

        return redirect()
            ->route('admin.messages.index')
            ->with('status', __('contact.messages.deleted'));
    }
}
