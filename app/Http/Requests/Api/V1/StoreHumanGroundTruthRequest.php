<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHumanGroundTruthRequest extends FormRequest
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
            'source_file_id' => ['required', 'uuid', 'exists:source_files,id'],
            'output_type_id' => ['required', 'integer', 'exists:output_types,id'],
            'action_id' => ['nullable', 'integer', 'exists:automation_actions,id'],
            'content_text' => ['nullable', 'string'],
            'content_json' => ['nullable', 'array'],
            'storage_path' => ['nullable', 'string'],
            'approved_by' => ['required', 'uuid'],
            'approved_at' => ['nullable', 'date'],
            'approval_notes' => ['nullable', 'string'],
            'confidence_level' => ['nullable', 'string', 'max:20', Rule::in(['high', 'medium', 'low'])],
            'based_on_output_id' => ['nullable', 'uuid', 'exists:processed_outputs,id'],
            'based_on_cleaned_id' => ['nullable', 'uuid', 'exists:cleaned_outputs,id'],
            'is_active' => ['boolean'],
        ];
    }
}
