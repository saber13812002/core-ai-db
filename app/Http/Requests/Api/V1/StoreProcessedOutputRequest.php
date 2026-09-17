<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreProcessedOutputRequest extends FormRequest
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
            'job_id' => ['required', 'uuid', 'exists:automation_jobs,id'],
            'source_file_id' => ['required', 'uuid', 'exists:source_files,id'],
            'output_type_id' => ['required', 'integer', 'exists:output_types,id'],
            'action_id' => ['required', 'integer', 'exists:automation_actions,id'],
            'model_id' => ['nullable', 'integer', 'exists:models,id'],
            'prompt_id' => ['nullable', 'uuid', 'exists:prompts,id'],
            'content_text' => ['nullable', 'string'],
            'content_json' => ['nullable', 'array'],
            'storage_path' => ['nullable', 'string'],
            'content_hash' => ['nullable', 'string', 'max:64'],
            'chunk_refs' => ['nullable', 'array'],
            'token_count' => ['nullable', 'integer', 'min:0'],
            'char_count' => ['nullable', 'integer', 'min:0'],
            'quality_score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'is_human_approved' => ['boolean'],
            'human_approved_by' => ['nullable', 'uuid'],
            'human_approved_at' => ['nullable', 'date'],
            'human_notes' => ['nullable', 'string'],
            'version_number' => ['nullable', 'integer', 'min:1'],
            'superseded_by_id' => ['nullable', 'uuid', 'exists:processed_outputs,id'],
            'is_latest' => ['boolean'],
            'processing_metadata' => ['nullable', 'array'],
        ];
    }
}
