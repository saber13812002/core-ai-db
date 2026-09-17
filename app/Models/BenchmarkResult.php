<?php

namespace App\Models;

use Database\Factories\BenchmarkResultFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * A single benchmark comparison result (candidate vs baseline vs
 * optional ground truth) with scores in the 0-100 quality-rate scale.
 *
 * @property string $id
 * @property string $benchmark_session_id
 * @property string|null $candidate_output_id
 * @property string|null $candidate_cleaned_id
 * @property string|null $baseline_output_id
 * @property string|null $baseline_cleaned_id
 * @property string|null $ground_truth_id
 * @property float|null $overall_score
 * @property float|null $quality_rate
 * @property array<string, mixed>|null $metrics
 * @property array<string, mixed>|null $comparison_details
 * @property bool|null $is_candidate_better
 * @property float|null $improvement_percent
 * @property int|null $judge_model_id
 * @property string|null $judge_prompt_id
 * @property array<string, mixed>|null $judge_raw_response
 * @property bool $human_validated
 * @property string|null $human_validated_by
 * @property Carbon|null $human_validated_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Fillable([
    'benchmark_session_id', 'candidate_output_id', 'candidate_cleaned_id',
    'baseline_output_id', 'baseline_cleaned_id', 'ground_truth_id',
    'overall_score', 'quality_rate', 'metrics', 'comparison_details',
    'is_candidate_better', 'improvement_percent', 'judge_model_id',
    'judge_prompt_id', 'judge_raw_response', 'human_validated',
    'human_validated_by', 'human_validated_at',
])]
#[Table(name: 'benchmark_results')]
class BenchmarkResult extends Model
{
    /** @use HasFactory<BenchmarkResultFactory> */
    use HasFactory, HasUuids;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'overall_score' => 'decimal:2',
            'quality_rate' => 'decimal:2',
            'metrics' => 'array',
            'comparison_details' => 'array',
            'is_candidate_better' => 'boolean',
            'improvement_percent' => 'decimal:2',
            'judge_raw_response' => 'array',
            'human_validated' => 'boolean',
            'human_validated_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<BenchmarkSession, $this>
     */
    public function benchmarkSession(): BelongsTo
    {
        return $this->belongsTo(BenchmarkSession::class);
    }

    /**
     * @return BelongsTo<ProcessedOutput, $this>
     */
    public function candidateOutput(): BelongsTo
    {
        return $this->belongsTo(ProcessedOutput::class, 'candidate_output_id');
    }

    /**
     * @return BelongsTo<CleanedOutput, $this>
     */
    public function candidateCleaned(): BelongsTo
    {
        return $this->belongsTo(CleanedOutput::class, 'candidate_cleaned_id');
    }

    /**
     * @return BelongsTo<ProcessedOutput, $this>
     */
    public function baselineOutput(): BelongsTo
    {
        return $this->belongsTo(ProcessedOutput::class, 'baseline_output_id');
    }

    /**
     * @return BelongsTo<CleanedOutput, $this>
     */
    public function baselineCleaned(): BelongsTo
    {
        return $this->belongsTo(CleanedOutput::class, 'baseline_cleaned_id');
    }

    /**
     * @return BelongsTo<HumanGroundTruth, $this>
     */
    public function groundTruth(): BelongsTo
    {
        return $this->belongsTo(HumanGroundTruth::class, 'ground_truth_id');
    }

    /**
     * @return BelongsTo<AiModel, $this>
     */
    public function judgeModel(): BelongsTo
    {
        return $this->belongsTo(AiModel::class, 'judge_model_id');
    }

    /**
     * @return BelongsTo<MasterPrompt, $this>
     */
    public function judgePrompt(): BelongsTo
    {
        return $this->belongsTo(MasterPrompt::class, 'judge_prompt_id');
    }
}
