<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreModelReleaseRequest extends FormRequest
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
            'trained_model_id' => ['required', 'uuid', 'exists:trained_models,id'],
            'version' => ['required', 'string', 'max:50'],
            'status' => ['nullable', 'string', 'max:50', Rule::in(['draft', 'pending-approval', 'approved', 'rejected', 'deployed', 'retired'])],
            'approved_by' => ['nullable', 'uuid'],
            'approved_at' => ['nullable', 'date'],
            'rejection_reason' => ['nullable', 'string'],
            'release_notes' => ['nullable', 'string'],
            'performance_summary' => ['nullable', 'array'],
            'deployed_at' => ['nullable', 'date'],
            'retired_at' => ['nullable', 'date'],
        ];
    }
}
