<?php

namespace App\Http\Resources\Api\V1;

use App\Models\AutomationAction;
use App\Models\MasterPrompt;
use App\Models\OutputType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin MasterPrompt
 */
class MasterPromptResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'family_id' => $this->family_id,
            'name' => $this->name,
            'version' => $this->version,
            'content' => $this->content,
            'content_hash' => $this->content_hash,
            'prompt_type' => $this->prompt_type,
            'purpose' => $this->purpose,
            'target_output_type_id' => $this->target_output_type_id,
            'target_output_type' => $this->whenLoaded('targetOutputType', fn (OutputType $rel): array => [
                'id' => $rel->id,
                'code' => $rel->code,
                'name_fa' => $rel->name_fa,
            ]),
            'target_action_id' => $this->target_action_id,
            'target_action' => $this->whenLoaded('targetAction', fn (AutomationAction $rel): array => [
                'id' => $rel->id,
                'code' => $rel->code,
                'name_fa' => $rel->name_fa,
            ]),
            'parent_prompt_id' => $this->parent_prompt_id,
            'parent_prompt' => $this->whenLoaded('parentPrompt', fn (self $rel): array => [
                'id' => $rel->id,
                'name' => $rel->name,
                'version' => $rel->version,
            ]),
            'is_active' => $this->is_active,
            'tags' => $this->tags,
            'metadata' => $this->metadata,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
