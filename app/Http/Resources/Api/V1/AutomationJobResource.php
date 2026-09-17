<?php

namespace App\Http\Resources\Api\V1;

use App\Models\AutomationAction;
use App\Models\AutomationFlow;
use App\Models\AutomationJob;
use App\Models\MasterPrompt;
use App\Models\ServiceRegistry;
use App\Models\SourceFile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin AutomationJob
 */
class AutomationJobResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'batch_id' => $this->batch_id,
            'source_file_id' => $this->source_file_id,
            'source_file' => $this->whenLoaded('sourceFile', fn (SourceFile $rel): array => [
                'id' => $rel->id,
                'original_filename' => $rel->original_filename,
                'file_type' => $rel->file_type,
            ]),
            'action_id' => $this->action_id,
            'action' => $this->whenLoaded('action', fn (AutomationAction $rel): array => [
                'id' => $rel->id,
                'code' => $rel->code,
                'name_fa' => $rel->name_fa,
            ]),
            'flow_id' => $this->flow_id,
            'flow' => $this->whenLoaded('flow', fn (AutomationFlow $rel): array => [
                'id' => $rel->id,
                'name' => $rel->name,
                'platform' => $rel->platform,
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
            'cleaning_prompt_id' => $this->cleaning_prompt_id,
            'cleaning_prompt' => $this->whenLoaded('cleaningPrompt', fn (MasterPrompt $rel): array => [
                'id' => $rel->id,
                'name' => $rel->name,
                'version' => $rel->version,
            ]),
            'parent_job_id' => $this->parent_job_id,
            'rerun_reason' => $this->rerun_reason,
            'rerun_of_job_id' => $this->rerun_of_job_id,
            'status' => $this->status,
            'priority' => $this->priority,
            'progress_percent' => $this->progress_percent,
            'estimated_cost_usd' => $this->estimated_cost_usd,
            'estimated_duration_sec' => $this->estimated_duration_sec,
            'actual_cost_usd' => $this->actual_cost_usd,
            'actual_duration_sec' => $this->actual_duration_sec,
            'token_count' => $this->token_count,
            'service_id' => $this->service_id,
            'service' => $this->whenLoaded('service', fn (ServiceRegistry $rel): array => [
                'id' => $rel->id,
                'name' => $rel->name,
            ]),
            'external_job_id' => $this->external_job_id,
            'request_payload' => $this->request_payload,
            'response_payload' => $this->response_payload,
            'error_message' => $this->error_message,
            'retry_count' => $this->retry_count,
            'max_retries' => $this->max_retries,
            'queued_at' => $this->queued_at,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
        ];
    }
}
