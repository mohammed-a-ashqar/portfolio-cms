<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ReelProvider;
use App\Support\Concerns\HasTranslations;
use App\Support\Concerns\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Reel extends Model
{
    use HasFactory;
    use HasTranslations;
    use Sortable;

    /** @var array<int, string> */
    protected array $translatable = ['caption'];

    protected $fillable = [
        'provider', 'external_id', 'url', 'embed_url', 'caption', 'thumbnail_path',
        'thumbnail_url', 'author_name', 'project_id', 'duration_seconds', 'views_count',
        'likes_count', 'posted_at', 'synced_at', 'is_featured', 'is_active', 'order_column', 'metadata',
    ];

    protected function casts(): array
    {
        return [
            'provider' => ReelProvider::class,
            'posted_at' => 'datetime',
            'synced_at' => 'datetime',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'metadata' => 'array',
            'views_count' => 'integer',
            'likes_count' => 'integer',
            'duration_seconds' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeFromProvider(Builder $query, ReelProvider|string|null $provider): Builder
    {
        return $provider
            ? $query->where('provider', $provider instanceof ReelProvider ? $provider->value : $provider)
            : $query;
    }

    /**
     * A locally stored poster always wins over the platform's own thumbnail:
     * it survives the platform expiring its CDN links and loads far faster.
     */
    public function getPosterUrlAttribute(): ?string
    {
        if ($this->thumbnail_path) {
            return Storage::disk('public')->url($this->thumbnail_path);
        }

        return $this->thumbnail_url;
    }

    public function getDurationForHumansAttribute(): ?string
    {
        if ($this->duration_seconds === null) {
            return null;
        }

        return sprintf('%d:%02d', intdiv($this->duration_seconds, 60), $this->duration_seconds % 60);
    }
}
