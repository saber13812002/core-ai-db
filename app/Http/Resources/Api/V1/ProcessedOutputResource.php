<?php

namespace App\Http\Resources\Api\V1;

use App\Models\AutomationAction;
use App\Models\AutomationJob;
use App\Models\MasterPrompt;
use App\Models\OutputType;
use App\Models\ProcessedOutput;
use App\Models\SourceFile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ProcessedOutput
 */
class ProcessedOutputResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'job_id' => $this->job_id,
            'job' => $this->whenLoaded('job', fn (AutomationJob $rel): array => [
                'id' => $rel->id,
                'status' => $rel->status,
            ]),
            'source_file_id' => $this->source_file_id,
            'source_file' => $this->whenLoaded('sourceFile', fn (SourceFile $rel): array => [
                'id' => $rel->id,
                'original_filename' => $rel->original_filename,
                'file_type' => $rel->file_type,
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
            'model_id' => $this->model_id,
            'model' => $this->whenLoaded('model', fn ($rel): array => [
                'id' => $rel->id,
                'code' => $rel->code,
            ]),
            'prompt_id' => $this->prompt_id,
            'prompt' => $this->whenLoaded('prompt', fn (MasterPrompt $rel): array => [
                'id' => $rel->id,
                'name' => $rel->name,
                'version' => $rel->version,
            ]),
            'content_text' => $this->content_text,
            'content_json' => $this->content_json,
            'storage_path' => $this->storage_path,
            'content_hash' => $this->content_hash,
            'chunk_refs' => $this->chunk_refs,
            'token_count' => $this->token_count,
            'char_count' => $this->char_count,
            'quality_score' => $this->quality_score,
            'is_human_approved' => $this->is_human_approved,
            'human_approved_by' => $this->human_approved_by,
            'human_approved_at' => $this->human_approved_at,
            'human_notes' => $this->human_notes,
            'version_number' => $this->version_number,
            'superseded_by_id' => $this->superseded_by_id,
            'superseded_by' => $this->whenLoaded('supersededBy', fn (ProcessedOutput $rel): array => [
                'id' => $rel->id,
                'version_number' => $rel->version_number,
            ]),
            'is_latest' => $this->is_latest,
            'processing_metadata' => $this->processing_metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
