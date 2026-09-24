<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BillingPeriod;
use App\Support\Concerns\HasTranslations;
use App\Support\Concerns\Sortable;
use App\Support\Pricing\Money;
use App\Support\Pricing\PricingContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory;
    use HasTranslations;
    use Sortable;

    /** @var array<int, string> */
    protected array $translatable = ['name', 'description'];

    protected $fillable = [
        'service_id', 'name', 'description', 'price_minor', 'currency', 'billing_period',
        'features', 'delivery_days', 'revisions', 'is_popular', 'is_active', 'order_column',
    ];

    protected function casts(): array
    {
        return [
            'billing_period' => BillingPeriod::class,
            'features' => 'array',
            'price_minor' => 'integer',
            'delivery_days' => 'integer',
            'revisions' => 'integer',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function quoteRequests(): HasMany
    {
        return $this->hasMany(QuoteRequest::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function price(): Money
    {
        return new Money($this->price_minor, $this->currency);
    }

    /** Hands the pricing strategies everything they need, and nothing more. */
    public function pricingContext(int $quantity = 1, float $discountPercent = 0.0): PricingContext
    {
        return new PricingContext(
            basePrice: $this->price(),
            period: $this->billing_period,
            quantity: $quantity,
            discountPercent: $discountPercent,
        );
    }

    /** @return array<int, string> Features for the active locale. */
    public function localisedFeatures(?string $locale = null): array
    {
        $locale ??= app()->getLocale();
        $features = $this->features ?? [];

        return $features[$locale] ?? $features[config('app.fallback_locale')] ?? [];
    }
}
