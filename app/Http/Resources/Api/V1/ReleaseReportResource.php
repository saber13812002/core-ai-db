<?php

namespace App\Http\Resources\Api\V1;

use App\Models\ModelRelease;
use App\Models\ReleaseReport;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ReleaseReport
 */
class ReleaseReportResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'release_id' => $this->release_id,
            'release' => $this->whenLoaded('release', fn (ModelRelease $rel): array => [
                'id' => $rel->id,
                'version' => $rel->version,
                'status' => $rel->status,
            ]),
            'report_type' => $this->report_type,
            'content' => $this->content,
            'storage_path' => $this->storage_path,
            'generated_at' => $this->generated_at,
        ];
    }
}
