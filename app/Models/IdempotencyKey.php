<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * A replay-protection token for idempotent API calls. Binds an
 * idempotency key to the route it was used on and the resource it
 * produced, for a fixed TTL.
 *
 * @property int $id
 * @property string|null $api_key_id
 * @property string $key
 * @property string $route_path
 * @property string $reference_id
 * @property int $response_status
 * @property Carbon $expires_at
 * @property Carbon $created_at
 */
#[Fillable([
    'api_key_id', 'key', 'route_path', 'reference_id', 'response_status', 'expires_at',
])]
#[Table(name: 'idempotency_keys', timestamps: false)]
#[WithoutTimestamps]
class IdempotencyKey extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'response_status' => 'integer',
            'expires_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }
}
