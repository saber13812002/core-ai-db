<?php

namespace App\Models;

use Database\Factories\ApiKeyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * A shared-secret credential for API consumers. The plaintext key
 * is shown once at creation time and never stored; only its SHA-256
 * hash is kept.
 *
 * @property string $id
 * @property string $name
 * @property string $key_hash
 * @property array<string, mixed>|null $scopes
 * @property bool $is_active
 * @property Carbon|null $last_used_at
 * @property string|null $created_by
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Fillable([
    'name', 'key_hash', 'scopes', 'is_active', 'last_used_at', 'created_by',
])]
#[Table(name: 'api_keys')]
class ApiKey extends Model
{
    /** @use HasFactory<ApiKeyFactory> */
    use HasFactory, HasUuids;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'scopes' => 'array',
            'is_active' => 'boolean',
            'last_used_at' => 'datetime',
        ];
    }

    /**
     * Hash a plaintext API key for storage and comparison.
     */
    public static function hashKey(string $plainKey): string
    {
        return hash('sha256', $plainKey);
    }

    /**
     * Mark this key as used (best-effort, updated once per day).
     */
    public function markUsed(): void
    {
        if ($this->last_used_at === null || $this->last_used_at->lt(now()->subHour())) {
            $this->forceFill(['last_used_at' => now()])->save();
        }
    }
}
