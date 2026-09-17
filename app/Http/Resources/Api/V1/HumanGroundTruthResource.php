<?php

namespace App\Http\Resources\Api\V1;

use App\Models\AutomationAction;
use App\Models\CleanedOutput;
use App\Models\HumanGroundTruth;
use App\Models\OutputType;
use App\Models\ProcessedOutput;
use App\Models\SourceFile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin HumanGroundTruth
 */
class HumanGroundTruthResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'source_file_id' => $this->source_file_id,
            'source_file' => $this->whenLoaded('sourceFile', fn (SourceFile $rel): array => [
                'id' => $rel->id,
                'original_filename' => $rel->original_filename,
            ]),
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
            ]),
            'content_text' => $this->content_text,
            'content_json' => $this->content_json,
            'storage_path' => $this->storage_path,
            'approved_by' => $this->approved_by,
            'approved_at' => $this->approved_at,
            'approval_notes' => $this->approval_notes,
            'confidence_level' => $this->confidence_level,
            'based_on_output_id' => $this->based_on_output_id,
            'based_on_output' => $this->whenLoaded('basedOnOutput', fn (ProcessedOutput $rel): array => [
                'id' => $rel->id,
                'version_number' => $rel->version_number,
            ]),
            'based_on_cleaned_id' => $this->based_on_cleaned_id,
            'based_on_cleaned' => $this->whenLoaded('basedOnCleaned', fn (CleanedOutput $rel): array => [
                'id' => $rel->id,
                'version_number' => $rel->version_number,
            ]),
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
