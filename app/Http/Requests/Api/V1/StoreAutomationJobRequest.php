<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAutomationJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'batch_id' => ['nullable', 'uuid', 'exists:job_batches,id'],
            'source_file_id' => ['required', 'uuid', 'exists:source_files,id'],
            'action_id' => ['required', 'integer', 'exists:automation_actions,id'],
            'flow_id' => ['nullable', 'uuid', 'exists:automation_flows,id'],
            'model_id' => ['nullable', 'integer', 'exists:models,id'],
            'prompt_id' => ['nullable', 'uuid', 'exists:prompts,id'],
            'cleaning_prompt_id' => ['nullable', 'uuid', 'exists:prompts,id'],
            'parent_job_id' => ['nullable', 'uuid', 'exists:automation_jobs,id'],
            'rerun_reason' => ['nullable', 'string', 'max:50'],
            'rerun_of_job_id' => ['nullable', 'uuid', 'exists:automation_jobs,id'],
            'status' => ['nullable', 'string', 'max:50', 'in:queued,running,completed,failed,cancelled'],
            'priority' => ['nullable', 'integer'],
            'progress_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'estimated_cost_usd' => ['nullable', 'numeric', 'min:0'],
            'estimated_duration_sec' => ['nullable', 'integer', 'min:0'],
            'actual_cost_usd' => ['nullable', 'numeric', 'min:0'],
            'actual_duration_sec' => ['nullable', 'integer', 'min:0'],
            'token_count' => ['nullable', 'integer', 'min:0'],
            'service_id' => ['nullable', 'integer', 'exists:service_registry,id'],
            'external_job_id' => ['nullable', 'string', 'max:200'],
            'source' => ['nullable', 'string', 'max:20', Rule::in(['api', 'webhook', 'scheduler', 'manual', 'flow'])],
            'is_automatic' => ['nullable', 'boolean'],
            'request_payload' => ['nullable', 'array'],
            'response_payload' => ['nullable', 'array'],
            'error_message' => ['nullable', 'string'],
            'retry_count' => ['nullable', 'integer', 'min:0'],
            'max_retries' => ['nullable', 'integer', 'min:0'],
            'started_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
            'created_by' => ['nullable', 'uuid'],
        ];
    }
}
