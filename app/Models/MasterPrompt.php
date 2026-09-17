<?php

namespace App\Models;

use Database\Factories\MasterPromptFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Git-like prompt versioning: every version row references the exact
 * content hash it was created from; outputs always reference the exact
 * prompt version id, never a "current" prompt.
 *
 * @property string $id
 * @property string $family_id
 * @property string $name
 * @property string $version
 * @property string $content
 * @property string $content_hash
 * @property string $prompt_type
 * @property string|null $purpose
 * @property int|null $target_output_type_id
 * @property int|null $target_action_id
 * @property string|null $parent_prompt_id
 * @property bool $is_active
 * @property array<int, string>|null $tags
 * @property array<string, mixed>|null $metadata
 * @property string|null $created_by
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable([
    'family_id', 'name', 'version', 'content', 'content_hash', 'prompt_type',
    'purpose', 'target_output_type_id', 'target_action_id', 'parent_prompt_id',
    'is_active', 'tags', 'metadata', 'created_by',
])]
#[Table(name: 'prompts')]
class MasterPrompt extends Model
{
    /** @use HasFactory<MasterPromptFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'tags' => 'array',
            'metadata' => 'array',
        ];
    }

    /**
     * @return BelongsTo<OutputType, $this>
     */
    public function targetOutputType(): BelongsTo
    {
        return $this->belongsTo(OutputType::class, 'target_output_type_id');
    }

    /**
     * @return BelongsTo<AutomationAction, $this>
     */
    public function targetAction(): BelongsTo
    {
        return $this->belongsTo(AutomationAction::class, 'target_action_id');
    }

    /**
     * @return BelongsTo<MasterPrompt, $this>
     */
    public function parentPrompt(): BelongsTo
    {
        return $this->belongsTo(MasterPrompt::class, 'parent_prompt_id');
    }

    /**
     * @return HasMany<MasterPrompt, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(MasterPrompt::class, 'parent_prompt_id');
    }

    /**
     * @return HasMany<AutomationJob, $this>
     */
    public function automationJobs(): HasMany
    {
        return $this->hasMany(AutomationJob::class, 'prompt_id');
    }

    /**
     * @return HasMany<AutomationJob, $this>
     */
    public function cleaningJobs(): HasMany
    {
        return $this->hasMany(AutomationJob::class, 'cleaning_prompt_id');
    }

    /**
     * @return HasMany<ProcessedOutput, $this>
     */
    public function processedOutputs(): HasMany
    {
        return $this->hasMany(ProcessedOutput::class);
    }

    /**
     * @return HasMany<CleanedOutput, $this>
     */
    public function cleanedOutputs(): HasMany
    {
        return $this->hasMany(CleanedOutput::class, 'cleaning_prompt_id');
    }

    /**
     * @return HasMany<BenchmarkSession, $this>
     */
    public function benchmarkSessions(): HasMany
    {
        return $this->hasMany(BenchmarkSession::class, 'judge_prompt_id');
    }

    /**
     * @return HasMany<BenchmarkResult, $this>
     */
    public function benchmarkResults(): HasMany
    {
        return $this->hasMany(BenchmarkResult::class, 'judge_prompt_id');
    }

    /**
     * @return HasMany<FeedbackLog, $this>
     */
    public function feedbackLogs(): HasMany
    {
        return $this->hasMany(FeedbackLog::class);
    }
}
