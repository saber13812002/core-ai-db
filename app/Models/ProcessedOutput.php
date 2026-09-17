<?php

namespace App\Models;

use Database\Factories\ProcessedOutputFactory;
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
 * An immutable AI-produced output. A correction creates a new version row;
 * the original is never updated in place.
 *
 * @property string $id
 * @property string $job_id
 * @property string $source_file_id
 * @property int $output_type_id
 * @property int $action_id
 * @property int|null $model_id
 * @property string|null $prompt_id
 * @property string|null $content_text
 * @property array<string, mixed>|null $content_json
 * @property string|null $storage_path
 * @property string|null $content_hash
 * @property array<int, mixed>|null $chunk_refs
 * @property int|null $token_count
 * @property int|null $char_count
 * @property float|null $quality_score
 * @property bool $is_human_approved
 * @property string|null $human_approved_by
 * @property Carbon|null $human_approved_at
 * @property string|null $human_notes
 * @property int $version_number
 * @property string|null $superseded_by_id
 * @property bool $is_latest
 * @property array<string, mixed>|null $processing_metadata
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable([
    'job_id', 'source_file_id', 'output_type_id', 'action_id', 'model_id', 'prompt_id',
    'content_text', 'content_json', 'storage_path', 'content_hash', 'chunk_refs',
    'token_count', 'char_count', 'quality_score', 'is_human_approved',
    'human_approved_by', 'human_approved_at', 'human_notes', 'version_number',
    'superseded_by_id', 'is_latest', 'processing_metadata',
])]
#[Table(name: 'processed_outputs')]
class ProcessedOutput extends Model
{
    /** @use HasFactory<ProcessedOutputFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'content_json' => 'array',
            'chunk_refs' => 'array',
            'token_count' => 'integer',
            'char_count' => 'integer',
            'quality_score' => 'decimal:2',
            'is_human_approved' => 'boolean',
            'human_approved_at' => 'datetime',
            'version_number' => 'integer',
            'is_latest' => 'boolean',
            'processing_metadata' => 'array',
        ];
    }

    /**
     * @return BelongsTo<AutomationJob, $this>
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(AutomationJob::class, 'job_id');
    }

    /**
     * @return BelongsTo<SourceFile, $this>
     */
    public function sourceFile(): BelongsTo
    {
        return $this->belongsTo(SourceFile::class);
    }

    /**
     * @return BelongsTo<OutputType, $this>
     */
    public function outputType(): BelongsTo
    {
        return $this->belongsTo(OutputType::class);
    }

    /**
     * @return BelongsTo<AutomationAction, $this>
     */
    public function action(): BelongsTo
    {
        return $this->belongsTo(AutomationAction::class, 'action_id');
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
     * @return BelongsTo<ProcessedOutput, $this>
     */
    public function supersededBy(): BelongsTo
    {
        return $this->belongsTo(ProcessedOutput::class, 'superseded_by_id');
    }

    /**
     * @return HasMany<CleanedOutput, $this>
     */
    public function cleanedOutputs(): HasMany
    {
        return $this->hasMany(CleanedOutput::class);
    }

    /**
     * @return HasMany<HumanGroundTruth, $this>
     */
    public function humanGroundTruths(): HasMany
    {
        return $this->hasMany(HumanGroundTruth::class, 'based_on_output_id');
    }

    /**
     * @return HasMany<BenchmarkResult, $this>
     */
    public function candidateBenchmarkResults(): HasMany
    {
        return $this->hasMany(BenchmarkResult::class, 'candidate_output_id');
    }

    /**
     * @return HasMany<BenchmarkResult, $this>
     */
    public function baselineBenchmarkResults(): HasMany
    {
        return $this->hasMany(BenchmarkResult::class, 'baseline_output_id');
    }

    /**
     * @return HasMany<DatasetItem, $this>
     */
    public function datasetItems(): HasMany
    {
        return $this->hasMany(DatasetItem::class, 'input_output_id');
    }

    /**
     * @return HasMany<VectorCollectionItem, $this>
     */
    public function vectorCollectionItems(): HasMany
    {
        return $this->hasMany(VectorCollectionItem::class);
    }

    /**
     * @return HasMany<FeedbackLog, $this>
     */
    public function feedbackLogs(): HasMany
    {
        return $this->hasMany(FeedbackLog::class);
    }
}
