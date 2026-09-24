<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\QuoteStatus;
use App\Support\Pricing\Money;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class QuoteRequest extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'reference', 'service_id', 'package_id', 'name', 'email', 'phone', 'company',
        'budget_min_minor', 'budget_max_minor', 'currency', 'message', 'status',
        'quoted_amount_minor', 'admin_notes', 'responded_at', 'ip_address', 'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'status' => QuoteStatus::class,
            'responded_at' => 'datetime',
            'budget_min_minor' => 'integer',
            'budget_max_minor' => 'integer',
            'quoted_amount_minor' => 'integer',
        ];
    }

    /** Sequential, human-quotable reference: QR-2026-0042. */
    public static function nextReference(?Carbon $now = null): string
    {
        $now ??= Carbon::now();
        $year = $now->year;

        $count = static::withTrashed()->whereYear('created_at', $year)->count() + 1;

        return sprintf('QR-%d-%04d', $year, $count);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', [
            QuoteStatus::New->value,
            QuoteStatus::InReview->value,
            QuoteStatus::Quoted->value,
        ]);
    }

    public function scopeWithStatus(Builder $query, QuoteStatus|string|null $status): Builder
    {
        return $status ? $query->where('status', $status instanceof QuoteStatus ? $status->value : $status) : $query;
    }

    public function budgetRange(): ?string
    {
        if ($this->budget_min_minor === null && $this->budget_max_minor === null) {
            return null;
        }

        $min = new Money((int) ($this->budget_min_minor ?? 0), $this->currency);
        $max = new Money((int) ($this->budget_max_minor ?? $this->budget_min_minor ?? 0), $this->currency);

        return $min->amountInMinorUnits === $max->amountInMinorUnits
            ? $min->format()
            : "{$min->format()} – {$max->format()}";
    }

    public function quotedAmount(): ?Money
    {
        return $this->quoted_amount_minor !== null
            ? new Money($this->quoted_amount_minor, $this->currency)
            : null;
    }
}
