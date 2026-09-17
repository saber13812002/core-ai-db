<?php

namespace App\Http\Resources\Api\V1;

use App\Models\AutomationAction;
use App\Models\AutomationFlow;
use App\Models\OutputType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin AutomationFlow
 */
class AutomationFlowResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'platform' => $this->platform,
            'platform_flow_id' => $this->platform_flow_id,
            'description' => $this->description,
            'input_file_types' => $this->input_file_types,
            'output_type_id' => $this->output_type_id,
            'output_type' => $this->whenLoaded('outputType', fn (OutputType $rel): array => [
                'id' => $rel->id,
                'code' => $rel->code,
                'name_fa' => $rel->name_fa,
            ]),
            'action_id' => $this->action_id,
            'action' => $this->whenLoaded('action', fn (AutomationAction $rel): array => [
                'id' => $rel->id,
                'code' => $rel->code,
                'name_fa' => $rel->name_fa,
            ]),
            'config' => $this->config,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
