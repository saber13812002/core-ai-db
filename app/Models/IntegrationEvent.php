<?php

namespace App\Models;

use Database\Factories\IntegrationEventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Raw log of every callback received from an external service
 * (OCR, fine-tuning, embedding, ...). The (reference_type, reference_id)
 * pair always names one row of a main table, so external services report
 * status using the UUIDs we own.
 *
 * @property int $id
 * @property int|null $service_id
 * @property string $reference_type
 * @property string $reference_id
 * @property string|null $external_job_id
 * @property string $event_type
 * @property array<string, mixed>|null $payload
 * @property Carbon $received_at
 * @property Carbon $created_at
 */
#[Fillable([
    'service_id', 'reference_type', 'reference_id', 'external_job_id',
    'event_type', 'payload', 'received_at',
])]
#[Table(name: 'integration_events', timestamps: false)]
#[WithoutTimestamps]
class IntegrationEvent extends Model
{
    /** @use HasFactory<IntegrationEventFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'received_at' => 'datetime',
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
     * The referenced model instance, resolved by reference_type.
     */
    public function referencedModel(): ?Model
    {
        $class = match ($this->reference_type) {
            'source_file' => SourceFile::class,
            'automation_job' => AutomationJob::class,
            'processed_output' => ProcessedOutput::class,
            'cleaned_output' => CleanedOutput::class,
            'dataset' => Dataset::class,
            'trained_model' => TrainedModel::class,
            'job_batch' => JobBatch::class,
            default => null,
        };

        return $class === null ? null : $class::find($this->reference_id);
    }
}
