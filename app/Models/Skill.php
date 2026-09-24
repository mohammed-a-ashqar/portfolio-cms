<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Concerns\HasSlug;
use App\Support\Concerns\HasTranslations;
use App\Support\Concerns\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;
    use HasSlug;
    use HasTranslations;
    use Sortable;

    /** @var array<int, string> */
    protected array $translatable = ['name', 'description'];

    protected $fillable = [
        'name', 'slug', 'group', 'proficiency', 'icon', 'description', 'order_column', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'proficiency' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function slugSource(): string
    {
        return 'name';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeInGroup(Builder $query, ?string $group): Builder
    {
        return $group ? $query->where('group', $group) : $query;
    }
}
