<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Concerns\HasTranslations;
use App\Support\Concerns\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Testimonial extends Model
{
    use HasFactory;
    use HasTranslations;
    use Sortable;

    /** @var array<int, string> */
    protected array $translatable = ['content', 'author_title'];

    protected $fillable = [
        'project_id', 'author_name', 'author_title', 'author_company', 'author_avatar',
        'author_url', 'content', 'rating', 'is_featured', 'is_active', 'order_column',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
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

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->author_avatar ? Storage::disk('public')->url($this->author_avatar) : null;
    }
}
