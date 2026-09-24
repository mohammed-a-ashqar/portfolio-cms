<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\MessageStatus;
use App\Models\ContactMessage;
use App\Repositories\Contracts\ContactMessageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/** @extends BaseRepository<ContactMessage> */
final class ContactMessageRepository extends BaseRepository implements ContactMessageRepositoryInterface
{
    public function __construct(ContactMessage $model)
    {
        parent::__construct($model);
    }

    public function listing(?MessageStatus $status = null, ?string $search = null, int $perPage = 20): LengthAwarePaginator
    {
        return $this->query()
            ->when($status, fn (Builder $q) => $q->where('status', $status))
            ->when($search, function (Builder $query, string $term): void {
                $query->where(function (Builder $q) use ($term): void {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('subject', 'like', "%{$term}%");
                });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function unreadCount(): int
    {
        return $this->query()->unread()->count();
    }

    public function recentCountForEmail(string $email, int $minutes = 60): int
    {
        return $this->query()
            ->where('email', $email)
            ->where('created_at', '>=', Carbon::now()->subMinutes($minutes))
            ->count();
    }
}
