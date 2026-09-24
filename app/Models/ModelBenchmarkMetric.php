<?php

namespace App\Models;

use Database\Factories\ModelBenchmarkMetricFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * One row per scored metric of a model evaluation. Each metric is judged
 * by the prompt dedicated to that metric (metric_prompt_id), so a model
 * benchmark can combine several judge prompts under one evaluation.
 *
 * @property string $id
 * @property string $model_evaluation_id
 * @property string $metric_name
 * @property string|null $metric_prompt_id
 * @property float|null $score
 * @property int|null $judge_model_id
 * @property array<string, mixed>|null $details
 * @property Carbon $evaluated_at
 * @property Carbon $created_at
 */
#[Fillable([
    'model_evaluation_id', 'metric_name', 'metric_prompt_id',
    'score', 'judge_model_id', 'details', 'evaluated_at',
])]
#[Table(name: 'model_benchmark_metrics', timestamps: false)]
#[WithoutTimestamps]
class ModelBenchmarkMetric extends Model
{
    /** @use HasFactory<ModelBenchmarkMetricFactory> */
    use HasFactory, HasUuids;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'details' => 'array',
            'evaluated_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<ModelEvaluation, $this>
     */
    public function modelEvaluation(): BelongsTo
    {
        return $this->belongsTo(ModelEvaluation::class);
    }

    /**
     * @return BelongsTo<MasterPrompt, $this>
     */
    public function metricPrompt(): BelongsTo
    {
        return $this->belongsTo(MasterPrompt::class, 'metric_prompt_id');
    }

    /**
     * @return BelongsTo<AiModel, $this>
     */
    public function judgeModel(): BelongsTo
    {
        return $this->belongsTo(AiModel::class, 'judge_model_id');
    }
}
