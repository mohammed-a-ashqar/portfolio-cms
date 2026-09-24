<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Enums\MessageStatus;
use App\Models\ContactMessage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/** @extends RepositoryInterface<ContactMessage> */
interface ContactMessageRepositoryInterface extends RepositoryInterface
{
    /** @return LengthAwarePaginator<ContactMessage> */
    public function listing(?MessageStatus $status = null, ?string $search = null, int $perPage = 20): LengthAwarePaginator;

    public function unreadCount(): int;

    /** Throttle guard: how many messages this address sent in the window. */
    public function recentCountForEmail(string $email, int $minutes = 60): int;
}
