<?php

namespace App\Models;

use Database\Factories\JobBatchFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * A nightly/scheduled group of automation jobs. The estimate is recorded
 * before execution and reported to the administrator; the actuals are
 * filled after completion. master_prompt_id pins the exact prompt version
 * the whole batch ran under.
 *
 * @property string $id
 * @property string|null $name
 * @property string|null $master_prompt_id
 * @property Carbon|null $scheduled_for
 * @property int|null $estimated_total_tokens
 * @property int|null $estimated_duration_seconds
 * @property int|null $actual_total_tokens
 * @property int|null $actual_duration_seconds
 * @property string $status
 * @property string|null $triggered_by
 * @property Carbon|null $completed_at
 * @property Carbon $created_at
 */
#[Fillable([
    'name', 'master_prompt_id', 'scheduled_for',
    'estimated_total_tokens', 'estimated_duration_seconds',
    'actual_total_tokens', 'actual_duration_seconds',
    'status', 'triggered_by', 'completed_at',
])]
#[Table(name: 'job_batches', timestamps: false)]
#[WithoutTimestamps]
class JobBatch extends Model
{
    /** @use HasFactory<JobBatchFactory> */
    use HasFactory, HasUuids;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'scheduled_for' => 'datetime',
            'estimated_total_tokens' => 'integer',
            'estimated_duration_seconds' => 'integer',
            'actual_total_tokens' => 'integer',
            'actual_duration_seconds' => 'integer',
            'completed_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<MasterPrompt, $this>
     */
    public function masterPrompt(): BelongsTo
    {
        return $this->belongsTo(MasterPrompt::class, 'master_prompt_id');
    }

    /**
     * @return HasMany<AutomationJob, $this>
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(AutomationJob::class, 'batch_id');
    }

    /**
     * @return HasMany<IntegrationEvent, $this>
     */
    public function integrationEvents(): HasMany
    {
        return $this->hasMany(IntegrationEvent::class, 'reference_id')
            ->where('reference_type', 'job_batch');
    }
}
