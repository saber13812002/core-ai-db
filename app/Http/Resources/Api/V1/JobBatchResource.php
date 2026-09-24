<?php

namespace App\Http\Resources\Api\V1;

use App\Models\JobBatch;
use App\Models\MasterPrompt;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin JobBatch
 */
class JobBatchResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'master_prompt_id' => $this->master_prompt_id,
            'master_prompt' => $this->whenLoaded('masterPrompt', fn (MasterPrompt $rel): array => [
                'id' => $rel->id,
                'name' => $rel->name,
                'version' => $rel->version,
                'family_id' => $rel->family_id,
            ]),
            'scheduled_for' => $this->scheduled_for,
            'estimated_total_tokens' => $this->estimated_total_tokens,
            'estimated_duration_seconds' => $this->estimated_duration_seconds,
            'actual_total_tokens' => $this->actual_total_tokens,
            'actual_duration_seconds' => $this->actual_duration_seconds,
            'status' => $this->status,
            'triggered_by' => $this->triggered_by,
            'completed_at' => $this->completed_at,
            'created_at' => $this->created_at,
        ];
    }
}
