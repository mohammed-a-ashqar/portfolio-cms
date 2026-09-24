<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ProjectStatus;
use App\Support\Concerns\HasSlug;
use App\Support\Concerns\HasTranslations;
use App\Support\Concerns\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * @property-read string|null $title  Resolved to the active locale by HasTranslations.
 */
class Project extends Model
{
    use HasFactory;
    use HasSlug;
    use HasTranslations;
    use SoftDeletes;
    use Sortable;

    /** @var array<int, string> */
    protected array $translatable = ['title', 'summary', 'description'];

    protected $fillable = [
        'category_id', 'title', 'slug', 'summary', 'description', 'cover_image',
        'client_name', 'project_url', 'repository_url', 'started_at', 'completed_at',
        'status', 'is_featured', 'order_column', 'seo',
    ];

    protected function casts(): array
    {
        return [
            'status' => ProjectStatus::class,
            'is_featured' => 'boolean',
            'started_at' => 'date',
            'completed_at' => 'date',
            'seo' => 'array',
            'views_count' => 'integer',
            'order_column' => 'integer',
        ];
    }

    // ----------------------------------------------------------------- relations

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(Technology::class)->orderBy('order_column');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('order_column');
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    public function reels(): HasMany
    {
        return $this->hasMany(Reel::class);
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    // -------------------------------------------------------------------- scopes

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', ProjectStatus::Published);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeInCategory(Builder $query, Category|int|string|null $category): Builder
    {
        if ($category === null) {
            return $query;
        }

        return $category instanceof Category
            ? $query->where('category_id', $category->getKey())
            : $query->whereHas('category', fn (Builder $q) => $q->where('slug', $category)->orWhereKey($category));
    }

    public function scopeUsingTechnology(Builder $query, string $slug): Builder
    {
        return $query->whereHas('technologies', fn (Builder $q) => $q->where('slug', $slug));
    }

    // ------------------------------------------------------------------ accessors

    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover_image ? Storage::disk('public')->url($this->cover_image) : null;
    }

    public function isPublished(): bool
    {
        return $this->status->isVisibleToPublic();
    }

    /** Atomic increment — avoids the read-modify-write race on popular projects. */
    public function recordView(): void
    {
        $this->newQuery()->whereKey($this->getKey())->increment('views_count');
    }
}
