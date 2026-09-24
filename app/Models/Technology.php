<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Concerns\HasSlug;
use App\Support\Concerns\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Technology extends Model
{
    use HasFactory;
    use HasSlug;
    use Sortable;

    protected $fillable = ['name', 'slug', 'icon', 'color', 'order_column'];

    public function slugSource(): string
    {
        return 'name';
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }
}
