<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIntegrationEventRequest extends FormRequest
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
            'service_id' => ['nullable', 'integer', 'exists:service_registry,id'],
            'reference_type' => ['required', 'string', 'max:50', Rule::in([
                'source_file', 'automation_job', 'processed_output', 'cleaned_output',
                'dataset', 'trained_model', 'job_batch',
            ])],
            'reference_id' => ['required', 'uuid'],
            'external_job_id' => ['nullable', 'string', 'max:200'],
            'event_type' => ['required', 'string', 'max:50'],
            'payload' => ['nullable', 'array'],
            'received_at' => ['nullable', 'date'],
        ];
    }
}
