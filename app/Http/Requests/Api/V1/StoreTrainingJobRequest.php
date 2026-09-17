<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTrainingJobRequest extends FormRequest
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
            'dataset_id' => ['required', 'uuid', 'exists:datasets,id'],
            'service_id' => ['nullable', 'integer', 'exists:service_registry,id'],
            'external_job_id' => ['nullable', 'string', 'max:200'],
            'base_model_code' => ['nullable', 'string', 'max:120'],
            'training_config' => ['nullable', 'array'],
            'status' => ['nullable', 'string', 'max:50', Rule::in(['queued', 'running', 'completed', 'failed', 'cancelled'])],
            'progress_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'estimated_cost_usd' => ['nullable', 'numeric', 'min:0'],
            'actual_cost_usd' => ['nullable', 'numeric', 'min:0'],
            'error_message' => ['nullable', 'string'],
            'started_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
        ];
    }
}
