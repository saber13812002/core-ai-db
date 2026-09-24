<?php

namespace App\Http\Resources\Api\V1;

use App\Models\IntegrationEvent;
use App\Models\ServiceRegistry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin IntegrationEvent
 */
class IntegrationEventResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'service_id' => $this->service_id,
            'service' => $this->whenLoaded('service', fn (ServiceRegistry $rel): array => [
                'id' => $rel->id,
                'name' => $rel->name,
                'service_type' => $rel->service_type,
            ]),
            'reference_type' => $this->reference_type,
            'reference_id' => $this->reference_id,
            'external_job_id' => $this->external_job_id,
            'event_type' => $this->event_type,
            'payload' => $this->payload,
            'received_at' => $this->received_at,
            'created_at' => $this->created_at,
        ];
    }
}
