<?php

namespace App\Http\Resources\Api\V1;

use App\Models\ServiceRegistry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ServiceRegistry
 */
class ServiceRegistryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'service_type' => $this->service_type,
            'base_url' => $this->base_url,
            'api_key_ref' => $this->api_key_ref,
            'endpoints' => $this->endpoints,
            'is_active' => $this->is_active,
            'health_status' => $this->health_status,
            'last_health_check' => $this->last_health_check,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
        ];
    }
}
