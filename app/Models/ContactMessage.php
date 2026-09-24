<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MessageStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactMessage extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name', 'email', 'phone', 'subject', 'message',
        'status', 'reply', 'replied_at', 'ip_address', 'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'status' => MessageStatus::class,
            'replied_at' => 'datetime',
        ];
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('status', MessageStatus::Unread);
    }

    public function markAsRead(): void
    {
        if ($this->status === MessageStatus::Unread) {
            $this->update(['status' => MessageStatus::Read]);
        }
    }
}
