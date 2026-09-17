<?php

namespace App\Models;

use Database\Factories\CleanedOutputFactory;
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
 * An immutable cleaned/normalized version of a processed output.
 *
 * @property string $id
 * @property string $processed_output_id
 * @property string $source_file_id
 * @property string $cleaning_prompt_id
 * @property int|null $model_id
 * @property string|null $content_text
 * @property array<string, mixed>|null $content_json
 * @property string|null $storage_path
 * @property string|null $content_hash
 * @property string|null $cleaning_type
 * @property float|null $quality_score
 * @property bool $is_human_approved
 * @property string|null $human_approved_by
 * @property Carbon|null $human_approved_at
 * @property int $version_number
 * @property string|null $superseded_by_id
 * @property bool $is_latest
 * @property array<string, mixed>|null $processing_metadata
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable([
    'processed_output_id', 'source_file_id', 'cleaning_prompt_id', 'model_id',
    'content_text', 'content_json', 'storage_path', 'content_hash', 'cleaning_type',
    'quality_score', 'is_human_approved', 'human_approved_by', 'human_approved_at',
    'version_number', 'superseded_by_id', 'is_latest', 'processing_metadata',
])]
#[Table(name: 'cleaned_outputs')]
class CleanedOutput extends Model
{
    /** @use HasFactory<CleanedOutputFactory> */
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
            'quality_score' => 'decimal:2',
            'is_human_approved' => 'boolean',
            'human_approved_at' => 'datetime',
            'version_number' => 'integer',
            'is_latest' => 'boolean',
            'processing_metadata' => 'array',
        ];
    }

    /**
     * @return BelongsTo<ProcessedOutput, $this>
     */
    public function processedOutput(): BelongsTo
    {
        return $this->belongsTo(ProcessedOutput::class);
    }

    /**
     * @return BelongsTo<SourceFile, $this>
     */
    public function sourceFile(): BelongsTo
    {
        return $this->belongsTo(SourceFile::class);
    }

    /**
     * @return BelongsTo<MasterPrompt, $this>
     */
    public function cleaningPrompt(): BelongsTo
    {
        return $this->belongsTo(MasterPrompt::class, 'cleaning_prompt_id');
    }

    /**
     * @return BelongsTo<AiModel, $this>
     */
    public function model(): BelongsTo
    {
        return $this->belongsTo(AiModel::class, 'model_id');
    }

    /**
     * @return BelongsTo<CleanedOutput, $this>
     */
    public function supersededBy(): BelongsTo
    {
        return $this->belongsTo(CleanedOutput::class, 'superseded_by_id');
    }

    /**
     * @return HasMany<HumanGroundTruth, $this>
     */
    public function humanGroundTruths(): HasMany
    {
        return $this->hasMany(HumanGroundTruth::class, 'based_on_cleaned_id');
    }

    /**
     * @return HasMany<BenchmarkResult, $this>
     */
    public function candidateBenchmarkResults(): HasMany
    {
        return $this->hasMany(BenchmarkResult::class, 'candidate_cleaned_id');
    }

    /**
     * @return HasMany<BenchmarkResult, $this>
     */
    public function baselineBenchmarkResults(): HasMany
    {
        return $this->hasMany(BenchmarkResult::class, 'baseline_cleaned_id');
    }

    /**
     * @return HasMany<DatasetItem, $this>
     */
    public function datasetItems(): HasMany
    {
        return $this->hasMany(DatasetItem::class, 'input_cleaned_id');
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
