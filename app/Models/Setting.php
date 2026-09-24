<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['group', 'key', 'value', 'type'];

    protected function casts(): array
    {
        return ['value' => 'array'];
    }

    /** Settings are stored as JSON; this unwraps them back to their declared type. */
    public function typedValue(): mixed
    {
        $raw = $this->value['value'] ?? null;

        return match ($this->type) {
            'bool' => (bool) $raw,
            'int' => (int) $raw,
            'json', 'translatable' => $raw,
            default => $raw === null ? null : (string) $raw,
        };
    }
}
