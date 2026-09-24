<?php

namespace App\Http\Resources\Api\V1;

use App\Models\BenchmarkSession;
use App\Models\MasterPrompt;
use App\Models\ModelEvaluation;
use App\Models\TrainedModel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ModelEvaluation
 */
class ModelEvaluationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'trained_model_id' => $this->trained_model_id,
            'trained_model' => $this->whenLoaded('trainedModel', fn (TrainedModel $rel): array => [
                'id' => $rel->id,
                'name' => $rel->name,
                'version' => $rel->version,
            ]),
            'benchmark_session_id' => $this->benchmark_session_id,
            'benchmark_session' => $this->whenLoaded('benchmarkSession', fn (BenchmarkSession $rel): array => [
                'id' => $rel->id,
                'name' => $rel->name,
            ]),
            'evaluation_type' => $this->evaluation_type,
            'overall_score' => $this->overall_score,
            'metrics' => $this->metrics,
            'metric_rows' => $this->whenLoaded('metricRows', fn ($rel): array => $rel->map(fn ($metric) => [
                'id' => $metric->id,
                'metric_name' => $metric->metric_name,
                'metric_prompt_id' => $metric->metric_prompt_id,
                'score' => $metric->score,
                'judge_model_id' => $metric->judge_model_id,
                'evaluated_at' => $metric->evaluated_at,
            ])->all()),
            'baseline_model_id' => $this->baseline_model_id,
            'baseline_model' => $this->whenLoaded('baselineModel', fn (TrainedModel $rel): array => [
                'id' => $rel->id,
                'name' => $rel->name,
                'version' => $rel->version,
            ]),
            'improvement_percent' => $this->improvement_percent,
            'is_better_than_baseline' => $this->is_better_than_baseline,
            'judge_model_id' => $this->judge_model_id,
            'judge_model' => $this->whenLoaded('judgeModel', fn ($rel): array => [
                'id' => $rel->id,
                'code' => $rel->code,
            ]),
            'judge_prompt_id' => $this->judge_prompt_id,
            'judge_prompt' => $this->whenLoaded('judgePrompt', fn (MasterPrompt $rel): array => [
                'id' => $rel->id,
                'name' => $rel->name,
            ]),
            'details' => $this->details,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
