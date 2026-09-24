<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\ContactMessageReceived;
use App\Events\ProjectPublished;
use App\Events\QuoteRequestSubmitted;
use App\Listeners\FlushPortfolioCache;
use App\Listeners\NotifyOwnerOfContactMessage;
use App\Listeners\NotifyOwnerOfQuoteRequest;
use App\Models\ContactMessage;
use App\Models\Project;
use App\Models\QuoteRequest;
use App\Models\Reel;
use App\Models\Service;
use App\Policies\ContactMessagePolicy;
use App\Policies\ProjectPolicy;
use App\Policies\QuoteRequestPolicy;
use App\Policies\ReelPolicy;
use App\Policies\ServicePolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /** @var array<class-string, class-string> */
    private const POLICIES = [
        Project::class => ProjectPolicy::class,
        Service::class => ServicePolicy::class,
        Reel::class => ReelPolicy::class,
        QuoteRequest::class => QuoteRequestPolicy::class,
        ContactMessage::class => ContactMessagePolicy::class,
    ];

    /** @var array<class-string, array<int, class-string>> */
    private const LISTENERS = [
        QuoteRequestSubmitted::class => [NotifyOwnerOfQuoteRequest::class],
        ContactMessageReceived::class => [NotifyOwnerOfContactMessage::class],
        ProjectPublished::class => [FlushPortfolioCache::class],
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        foreach (self::LISTENERS as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }

        foreach (self::POLICIES as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }
}
