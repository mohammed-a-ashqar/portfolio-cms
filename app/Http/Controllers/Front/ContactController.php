<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Actions\Contact\SubmitContactMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Front\StoreContactMessageRequest;
use DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

final class ContactController extends Controller
{
    public function index(): View
    {
        return view('front.contact');
    }

    public function store(StoreContactMessageRequest $request, SubmitContactMessage $action): RedirectResponse
    {
        try {
            $action->handle($request->toData());
        } catch (DomainException $exception) {
            return back()->withInput()->withErrors(['message' => $exception->getMessage()]);
        }

        return back()->with('status', __('contact.form.success'));
    }
}
