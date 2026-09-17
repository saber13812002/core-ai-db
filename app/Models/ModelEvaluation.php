<?php

namespace App\Models;

use Database\Factories\ModelEvaluationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Evaluation of a trained model against a benchmark or a baseline model.
 *
 * @property string $id
 * @property string $trained_model_id
 * @property string|null $benchmark_session_id
 * @property string|null $evaluation_type
 * @property float|null $overall_score
 * @property array<string, mixed>|null $metrics
 * @property string|null $baseline_model_id
 * @property float|null $improvement_percent
 * @property bool|null $is_better_than_baseline
 * @property int|null $judge_model_id
 * @property string|null $judge_prompt_id
 * @property array<string, mixed>|null $details
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Fillable([
    'trained_model_id', 'benchmark_session_id', 'evaluation_type', 'overall_score',
    'metrics', 'baseline_model_id', 'improvement_percent', 'is_better_than_baseline',
    'judge_model_id', 'judge_prompt_id', 'details',
])]
#[Table(name: 'model_evaluations')]
class ModelEvaluation extends Model
{
    /** @use HasFactory<ModelEvaluationFactory> */
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
            'metrics' => 'array',
            'improvement_percent' => 'decimal:2',
            'is_better_than_baseline' => 'boolean',
            'details' => 'array',
        ];
    }

    /**
     * @return BelongsTo<TrainedModel, $this>
     */
    public function trainedModel(): BelongsTo
    {
        return $this->belongsTo(TrainedModel::class);
    }

    /**
     * @return BelongsTo<BenchmarkSession, $this>
     */
    public function benchmarkSession(): BelongsTo
    {
        return $this->belongsTo(BenchmarkSession::class);
    }

    /**
     * @return BelongsTo<TrainedModel, $this>
     */
    public function baselineModel(): BelongsTo
    {
        return $this->belongsTo(TrainedModel::class, 'baseline_model_id');
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
