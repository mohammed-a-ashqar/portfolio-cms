<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Concerns\HasSlug;
use App\Support\Concerns\HasTranslations;
use App\Support\Concerns\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Service extends Model
{
    use HasFactory;
    use HasSlug;
    use HasTranslations;
    use SoftDeletes;
    use Sortable;

    /** @var array<int, string> */
    protected array $translatable = ['title', 'excerpt', 'description'];

    protected $fillable = [
        'title', 'slug', 'excerpt', 'description', 'icon', 'cover_image',
        'is_featured', 'is_active', 'order_column', 'seo',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'seo' => 'array',
        ];
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class)->orderBy('order_column');
    }

    public function quoteRequests(): HasMany
    {
        return $this->hasMany(QuoteRequest::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover_image ? Storage::disk('public')->url($this->cover_image) : null;
    }
}
