<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Dataset;
use App\Models\ServiceRegistry;
use App\Models\TrainingJob;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin TrainingJob
 */
class TrainingJobResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'dataset_id' => $this->dataset_id,
            'dataset' => $this->whenLoaded('dataset', fn (Dataset $rel): array => [
                'id' => $rel->id,
                'name' => $rel->name,
            ]),
            'service_id' => $this->service_id,
            'service' => $this->whenLoaded('service', fn (ServiceRegistry $rel): array => [
                'id' => $rel->id,
                'name' => $rel->name,
            ]),
            'external_job_id' => $this->external_job_id,
            'base_model_code' => $this->base_model_code,
            'training_config' => $this->training_config,
            'status' => $this->status,
            'progress_percent' => $this->progress_percent,
            'estimated_cost_usd' => $this->estimated_cost_usd,
            'actual_cost_usd' => $this->actual_cost_usd,
            'error_message' => $this->error_message,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
