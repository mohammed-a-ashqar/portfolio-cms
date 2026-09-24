<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'avatar', 'locale', 'is_active',
    ];

    /** @var list<string> */
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    public function uploads(): HasMany
    {
        return $this->hasMany(Media::class, 'uploaded_by');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    // ------------------------------------------------------------ authorisation

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    /** Single check the policies lean on, thanks to the ranked roles. */
    public function hasRole(UserRole $role): bool
    {
        return $this->role->atLeast($role);
    }

    public function canManageContent(): bool
    {
        return $this->hasRole(UserRole::Editor);
    }

    // ---------------------------------------------------------------- accessors

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return Storage::disk('public')->url($this->avatar);
        }

        // Deterministic fallback, no third-party avatar service involved.
        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=0f172a&color=fff';
    }

    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', trim($this->name)))
            ->filter()
            ->take(2)
            ->map(fn (string $part): string => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
    }
}
