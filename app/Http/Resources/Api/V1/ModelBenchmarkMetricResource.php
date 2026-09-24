<?php

namespace App\Http\Resources\Api\V1;

use App\Models\MasterPrompt;
use App\Models\ModelBenchmarkMetric;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ModelBenchmarkMetric
 */
class ModelBenchmarkMetricResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'metric_name' => $this->metric_name,
            'metric_prompt_id' => $this->metric_prompt_id,
            'metric_prompt' => $this->whenLoaded('metricPrompt', fn (MasterPrompt $rel): array => [
                'id' => $rel->id,
                'name' => $rel->name,
                'version' => $rel->version,
            ]),
            'score' => $this->score,
            'judge_model_id' => $this->judge_model_id,
            'details' => $this->details,
            'evaluated_at' => $this->evaluated_at,
            'created_at' => $this->created_at,
        ];
    }
}
