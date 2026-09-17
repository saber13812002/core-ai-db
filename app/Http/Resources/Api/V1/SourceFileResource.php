<?php

namespace App\Http\Resources\Api\V1;

use App\Models\SourceFile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SourceFile
 */
class SourceFileResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'external_ref' => $this->external_ref,
            'file_type' => $this->file_type,
            'original_filename' => $this->original_filename,
            'storage_path' => $this->storage_path,
            'mime_type' => $this->mime_type,
            'file_size_bytes' => $this->file_size_bytes,
            'checksum_sha256' => $this->checksum_sha256,
            'duration_seconds' => $this->duration_seconds,
            'page_count' => $this->page_count,
            'language' => $this->language,
            'metadata' => $this->metadata,
            'human_approved' => $this->human_approved,
            'human_approved_by' => $this->human_approved_by,
            'human_approved_at' => $this->human_approved_at,
            'human_approval_note' => $this->human_approval_note,
            'processing_status' => $this->processing_status,
            'version_number' => $this->version_number,
            'superseded_by_id' => $this->superseded_by_id,
            'superseded_by' => $this->whenLoaded('supersededBy', fn (SourceFile $rel): array => [
                'id' => $rel->id,
                'original_filename' => $rel->original_filename,
                'version_number' => $rel->version_number,
            ]),
            'is_latest' => $this->is_latest,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
