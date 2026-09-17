<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTrainedModelRequest extends FormRequest
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
            'training_job_id' => ['nullable', 'uuid', 'exists:training_jobs,id'],
            'name' => ['required', 'string', 'max:200'],
            'version' => ['required', 'string', 'max:50'],
            'model_type' => ['nullable', 'string', 'max:50'],
            'base_model_code' => ['nullable', 'string', 'max:120'],
            'storage_path' => ['nullable', 'string'],
            'service_endpoint' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:50', Rule::in(['ready', 'training', 'failed', 'retired'])],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
