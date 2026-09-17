<?php

namespace App\Http\Resources\Api\V1;

use App\Models\AutomationAction;
use App\Models\OutputType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin AutomationAction
 */
class AutomationActionResource extends JsonResource
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
            'description' => $this->description,
            'action_category' => $this->action_category,
            'input_file_types' => $this->input_file_types,
            'output_type_id' => $this->output_type_id,
            'output_type' => $this->whenLoaded('outputType', fn (OutputType $rel): array => [
                'id' => $rel->id,
                'code' => $rel->code,
                'name_fa' => $rel->name_fa,
            ]),
            'requires_prompt' => $this->requires_prompt,
            'is_batchable' => $this->is_batchable,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
        ];
    }
}
