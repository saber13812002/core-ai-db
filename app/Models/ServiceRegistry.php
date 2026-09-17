<?php

namespace App\Models;

use Database\Factories\ServiceRegistryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * A registered external service (LLM provider, transcription API, ...).
 *
 * @property int $id
 * @property string $name
 * @property string $service_type
 * @property string|null $base_url
 * @property string|null $api_key_ref
 * @property array<string, mixed>|null $endpoints
 * @property bool $is_active
 * @property string $health_status
 * @property Carbon|null $last_health_check
 * @property array<string, mixed>|null $metadata
 * @property Carbon $created_at
 */
#[Fillable([
    'name', 'service_type', 'base_url', 'api_key_ref',
    'endpoints', 'is_active', 'health_status', 'last_health_check', 'metadata',
])]
#[Table(name: 'service_registry', timestamps: false)]
#[WithoutTimestamps]
class ServiceRegistry extends Model
{
    /** @use HasFactory<ServiceRegistryFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'endpoints' => 'array',
            'is_active' => 'boolean',
            'last_health_check' => 'datetime',
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * @return HasMany<AutomationJob, $this>
     */
    public function automationJobs(): HasMany
    {
        return $this->hasMany(AutomationJob::class);
    }

    /**
     * @return HasMany<TrainingJob, $this>
     */
    public function trainingJobs(): HasMany
    {
        return $this->hasMany(TrainingJob::class);
    }

    /**
     * @return HasMany<ServiceCallLog, $this>
     */
    public function serviceCallLogs(): HasMany
    {
        return $this->hasMany(ServiceCallLog::class);
    }
}
