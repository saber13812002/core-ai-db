<?php

namespace App\Http\Resources\Api\V1;

use App\Models\ModelRelease;
use App\Models\TrainedModel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ModelRelease
 */
class ModelReleaseResource extends JsonResource
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
            'version' => $this->version,
            'status' => $this->status,
            'approved_by' => $this->approved_by,
            'approved_at' => $this->approved_at,
            'rejection_reason' => $this->rejection_reason,
            'release_notes' => $this->release_notes,
            'performance_summary' => $this->performance_summary,
            'deployed_at' => $this->deployed_at,
            'retired_at' => $this->retired_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
