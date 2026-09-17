<?php

namespace App\Http\Resources\Api\V1;

use App\Models\AiModel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin AiModel
 */
class AiModelResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name_fa' => $this->name_fa,
            'provider' => $this->provider,
            'model_type' => $this->model_type,
            'version' => $this->version,
            'endpoint' => $this->endpoint,
            'context_window' => $this->context_window,
            'is_active' => $this->is_active,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
        ];
    }
}
