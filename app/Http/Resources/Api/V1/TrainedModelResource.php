<?php

namespace App\Http\Resources\Api\V1;

use App\Models\TrainedModel;
use App\Models\TrainingJob;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin TrainedModel
 */
class TrainedModelResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'training_job_id' => $this->training_job_id,
            'training_job' => $this->whenLoaded('trainingJob', fn (TrainingJob $rel): array => [
                'id' => $rel->id,
                'status' => $rel->status,
                'dataset_id' => $rel->dataset_id,
            ]),
            'name' => $this->name,
            'version' => $this->version,
            'model_type' => $this->model_type,
            'base_model_code' => $this->base_model_code,
            'storage_path' => $this->storage_path,
            'service_endpoint' => $this->service_endpoint,
            'status' => $this->status,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
