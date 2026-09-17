<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreServiceCallLogRequest extends FormRequest
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
            'source_file_id' => ['nullable', 'uuid', 'exists:source_files,id'],
            'job_id' => ['nullable', 'uuid', 'exists:automation_jobs,id'],
            'endpoint' => ['nullable', 'string', 'max:500'],
            'http_method' => ['nullable', 'string', 'max:10', Rule::in(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'])],
            'request_payload' => ['nullable', 'array'],
            'response_payload' => ['nullable', 'array'],
            'http_status' => ['nullable', 'integer', 'min:100', 'max:599'],
            'duration_ms' => ['nullable', 'integer', 'min:0'],
            'error_message' => ['nullable', 'string'],
        ];
    }
}
