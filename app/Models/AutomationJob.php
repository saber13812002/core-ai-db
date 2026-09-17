<?php

namespace App\Models;

use Database\Factories\AutomationJobFactory;
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
 * A single execution of an action against a source file. Carries
 * estimated/actual cost and duration per project rules.
 *
 * @property string $id
 * @property string|null $batch_id
 * @property string $source_file_id
 * @property int $action_id
 * @property string|null $flow_id
 * @property int|null $model_id
 * @property string|null $prompt_id
 * @property string|null $cleaning_prompt_id
 * @property string|null $parent_job_id
 * @property string|null $rerun_reason
 * @property string|null $rerun_of_job_id
 * @property string $status
 * @property int $priority
 * @property int $progress_percent
 * @property float|null $estimated_cost_usd
 * @property int|null $estimated_duration_sec
 * @property float|null $actual_cost_usd
 * @property int|null $actual_duration_sec
 * @property int|null $token_count
 * @property int|null $service_id
 * @property string|null $external_job_id
 * @property array<string, mixed>|null $request_payload
 * @property array<string, mixed>|null $response_payload
 * @property string|null $error_message
 * @property int $retry_count
 * @property int $max_retries
 * @property Carbon $queued_at
 * @property Carbon|null $started_at
 * @property Carbon|null $completed_at
 * @property Carbon $created_at
 * @property string|null $created_by
 */
#[Fillable([
    'batch_id', 'source_file_id', 'action_id', 'flow_id', 'model_id',
    'prompt_id', 'cleaning_prompt_id', 'parent_job_id', 'rerun_reason',
    'rerun_of_job_id', 'status', 'priority', 'progress_percent',
    'estimated_cost_usd', 'estimated_duration_sec', 'actual_cost_usd',
    'actual_duration_sec', 'token_count', 'service_id', 'external_job_id',
    'request_payload', 'response_payload', 'error_message', 'retry_count',
    'max_retries', 'queued_at', 'started_at', 'completed_at', 'created_by',
])]
#[Table(name: 'automation_jobs', timestamps: false)]
#[WithoutTimestamps]
class AutomationJob extends Model
{
    /** @use HasFactory<AutomationJobFactory> */
    use HasFactory, HasUuids;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'progress_percent' => 'integer',
            'estimated_cost_usd' => 'decimal:4',
            'estimated_duration_sec' => 'integer',
            'actual_cost_usd' => 'decimal:4',
            'actual_duration_sec' => 'integer',
            'token_count' => 'integer',
            'request_payload' => 'array',
            'response_payload' => 'array',
            'retry_count' => 'integer',
            'max_retries' => 'integer',
            'queued_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<SourceFile, $this>
     */
    public function sourceFile(): BelongsTo
    {
        return $this->belongsTo(SourceFile::class);
    }

    /**
     * @return BelongsTo<AutomationAction, $this>
     */
    public function action(): BelongsTo
    {
        return $this->belongsTo(AutomationAction::class, 'action_id');
    }

    /**
     * @return BelongsTo<AutomationFlow, $this>
     */
    public function flow(): BelongsTo
    {
        return $this->belongsTo(AutomationFlow::class, 'flow_id');
    }

    /**
     * @return BelongsTo<AiModel, $this>
     */
    public function model(): BelongsTo
    {
        return $this->belongsTo(AiModel::class, 'model_id');
    }

    /**
     * @return BelongsTo<MasterPrompt, $this>
     */
    public function prompt(): BelongsTo
    {
        return $this->belongsTo(MasterPrompt::class, 'prompt_id');
    }

    /**
     * @return BelongsTo<MasterPrompt, $this>
     */
    public function cleaningPrompt(): BelongsTo
    {
        return $this->belongsTo(MasterPrompt::class, 'cleaning_prompt_id');
    }

    /**
     * @return BelongsTo<ServiceRegistry, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(ServiceRegistry::class, 'service_id');
    }

    /**
     * @return BelongsTo<AutomationJob, $this>
     */
    public function parentJob(): BelongsTo
    {
        return $this->belongsTo(AutomationJob::class, 'parent_job_id');
    }

    /**
     * @return BelongsTo<AutomationJob, $this>
     */
    public function rerunOfJob(): BelongsTo
    {
        return $this->belongsTo(AutomationJob::class, 'rerun_of_job_id');
    }

    /**
     * @return HasMany<AutomationJob, $this>
     */
    public function childJobs(): HasMany
    {
        return $this->hasMany(AutomationJob::class, 'parent_job_id');
    }

    /**
     * @return HasMany<ProcessedOutput, $this>
     */
    public function processedOutputs(): HasMany
    {
        return $this->hasMany(ProcessedOutput::class);
    }

    /**
     * @return HasMany<ServiceCallLog, $this>
     */
    public function serviceCallLogs(): HasMany
    {
        return $this->hasMany(ServiceCallLog::class, 'job_id');
    }
}
