<?php

namespace App\Http\Resources\Api\V1;

use App\Models\CleanedOutput;
use App\Models\MasterPrompt;
use App\Models\ProcessedOutput;
use App\Models\SourceFile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CleanedOutput
 */
class CleanedOutputResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'processed_output_id' => $this->processed_output_id,
            'processed_output' => $this->whenLoaded('processedOutput', fn (ProcessedOutput $rel): array => [
                'id' => $rel->id,
                'output_type_id' => $rel->output_type_id,
                'version_number' => $rel->version_number,
            ]),
            'source_file_id' => $this->source_file_id,
            'source_file' => $this->whenLoaded('sourceFile', fn (SourceFile $rel): array => [
                'id' => $rel->id,
                'original_filename' => $rel->original_filename,
            ]),
            'cleaning_prompt_id' => $this->cleaning_prompt_id,
            'cleaning_prompt' => $this->whenLoaded('cleaningPrompt', fn (MasterPrompt $rel): array => [
                'id' => $rel->id,
                'name' => $rel->name,
                'version' => $rel->version,
            ]),
            'model_id' => $this->model_id,
            'model' => $this->whenLoaded('model', fn ($rel): array => [
                'id' => $rel->id,
                'code' => $rel->code,
            ]),
            'content_text' => $this->content_text,
            'content_json' => $this->content_json,
            'storage_path' => $this->storage_path,
            'content_hash' => $this->content_hash,
            'cleaning_type' => $this->cleaning_type,
            'quality_score' => $this->quality_score,
            'is_human_approved' => $this->is_human_approved,
            'human_approved_by' => $this->human_approved_by,
            'human_approved_at' => $this->human_approved_at,
            'version_number' => $this->version_number,
            'superseded_by_id' => $this->superseded_by_id,
            'superseded_by' => $this->whenLoaded('supersededBy', fn (CleanedOutput $rel): array => [
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
