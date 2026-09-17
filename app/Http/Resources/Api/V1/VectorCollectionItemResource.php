<?php

namespace App\Http\Resources\Api\V1;

use App\Models\CleanedOutput;
use App\Models\ProcessedOutput;
use App\Models\SourceFile;
use App\Models\VectorCollection;
use App\Models\VectorCollectionItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin VectorCollectionItem
 */
class VectorCollectionItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vector_collection_id' => $this->vector_collection_id,
            'vector_collection' => $this->whenLoaded('vectorCollection', fn (VectorCollection $rel): array => [
                'id' => $rel->id,
                'name' => $rel->name,
            ]),
            'source_file_id' => $this->source_file_id,
            'source_file' => $this->whenLoaded('sourceFile', fn (SourceFile $rel): array => [
                'id' => $rel->id,
                'original_filename' => $rel->original_filename,
                'file_type' => $rel->file_type,
            ]),
            'processed_output_id' => $this->processed_output_id,
            'processed_output' => $this->whenLoaded('processedOutput', fn (ProcessedOutput $rel): array => [
                'id' => $rel->id,
                'version_number' => $rel->version_number,
            ]),
            'cleaned_output_id' => $this->cleaned_output_id,
            'cleaned_output' => $this->whenLoaded('cleanedOutput', fn (CleanedOutput $rel): array => [
                'id' => $rel->id,
                'version_number' => $rel->version_number,
            ]),
            'external_vector_id' => $this->external_vector_id,
            'chunk_ref' => $this->chunk_ref,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
        ];
    }
}
