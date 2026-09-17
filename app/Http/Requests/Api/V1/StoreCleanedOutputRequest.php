<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreCleanedOutputRequest extends FormRequest
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
            'processed_output_id' => ['required', 'uuid', 'exists:processed_outputs,id'],
            'source_file_id' => ['required', 'uuid', 'exists:source_files,id'],
            'cleaning_prompt_id' => ['required', 'uuid', 'exists:prompts,id'],
            'model_id' => ['nullable', 'integer', 'exists:models,id'],
            'content_text' => ['nullable', 'string'],
            'content_json' => ['nullable', 'array'],
            'storage_path' => ['nullable', 'string'],
            'content_hash' => ['nullable', 'string', 'max:64'],
            'cleaning_type' => ['nullable', 'string', 'max:50'],
            'quality_score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'is_human_approved' => ['boolean'],
            'human_approved_by' => ['nullable', 'uuid'],
            'human_approved_at' => ['nullable', 'date'],
            'version_number' => ['nullable', 'integer', 'min:1'],
            'superseded_by_id' => ['nullable', 'uuid', 'exists:cleaned_outputs,id'],
            'is_latest' => ['boolean'],
            'processing_metadata' => ['nullable', 'array'],
        ];
    }
}
