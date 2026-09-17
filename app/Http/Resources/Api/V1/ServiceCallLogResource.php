<?php

namespace App\Http\Resources\Api\V1;

use App\Models\AutomationJob;
use App\Models\ServiceCallLog;
use App\Models\ServiceRegistry;
use App\Models\SourceFile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ServiceCallLog
 */
class ServiceCallLogResource extends JsonResource
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
            ]),
            'source_file_id' => $this->source_file_id,
            'source_file' => $this->whenLoaded('sourceFile', fn (SourceFile $rel): array => [
                'id' => $rel->id,
                'original_filename' => $rel->original_filename,
            ]),
            'job_id' => $this->job_id,
            'job' => $this->whenLoaded('job', fn (AutomationJob $rel): array => [
                'id' => $rel->id,
                'status' => $rel->status,
            ]),
            'endpoint' => $this->endpoint,
            'http_method' => $this->http_method,
            'request_payload' => $this->request_payload,
            'response_payload' => $this->response_payload,
            'http_status' => $this->http_status,
            'duration_ms' => $this->duration_ms,
            'error_message' => $this->error_message,
            'created_at' => $this->created_at,
        ];
    }
}
