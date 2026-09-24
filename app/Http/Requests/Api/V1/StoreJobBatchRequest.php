<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobBatchRequest extends FormRequest
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
            'name' => ['nullable', 'string', 'max:300'],
            'master_prompt_id' => ['nullable', 'uuid', 'exists:prompts,id'],
            'scheduled_for' => ['nullable', 'date'],
            'estimated_total_tokens' => ['nullable', 'integer', 'min:0'],
            'estimated_duration_seconds' => ['nullable', 'integer', 'min:0'],
            'actual_total_tokens' => ['nullable', 'integer', 'min:0'],
            'actual_duration_seconds' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'string', 'max:50', Rule::in(['queued', 'running', 'completed', 'failed', 'partially_failed'])],
            'triggered_by' => ['nullable', 'string', 'max:100'],
            'completed_at' => ['nullable', 'date'],
        ];
    }
}
