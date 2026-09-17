<?php

namespace App\Models;

use Database\Factories\ServiceCallLogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * An audit log of a single HTTP call made to an external service.
 *
 * @property int $id
 * @property int|null $service_id
 * @property string|null $source_file_id
 * @property string|null $job_id
 * @property string|null $endpoint
 * @property string|null $http_method
 * @property array<string, mixed>|null $request_payload
 * @property array<string, mixed>|null $response_payload
 * @property int|null $http_status
 * @property int|null $duration_ms
 * @property string|null $error_message
 * @property Carbon $created_at
 */
#[Fillable([
    'service_id', 'source_file_id', 'job_id', 'endpoint', 'http_method',
    'request_payload', 'response_payload', 'http_status', 'duration_ms',
    'error_message',
])]
#[Table(name: 'service_call_logs', timestamps: false)]
#[WithoutTimestamps]
class ServiceCallLog extends Model
{
    /** @use HasFactory<ServiceCallLogFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'request_payload' => 'array',
            'response_payload' => 'array',
            'http_status' => 'integer',
            'duration_ms' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<ServiceRegistry, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(ServiceRegistry::class, 'service_id');
    }

    /**
     * @return BelongsTo<SourceFile, $this>
     */
    public function sourceFile(): BelongsTo
    {
        return $this->belongsTo(SourceFile::class);
    }

    /**
     * @return BelongsTo<AutomationJob, $this>
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(AutomationJob::class, 'job_id');
    }
}
