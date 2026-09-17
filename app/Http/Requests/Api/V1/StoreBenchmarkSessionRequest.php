<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBenchmarkSessionRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:300'],
            'description' => ['nullable', 'string'],
            'benchmark_type' => ['required', 'string', 'max:50', Rule::in(['ab', 'vs-ground-truth'])],
            'source_file_id' => ['nullable', 'uuid', 'exists:source_files,id'],
            'output_type_id' => ['nullable', 'integer', 'exists:output_types,id'],
            'action_id' => ['nullable', 'integer', 'exists:automation_actions,id'],
            'judge_model_id' => ['nullable', 'integer', 'exists:models,id'],
            'judge_prompt_id' => ['nullable', 'uuid', 'exists:prompts,id'],
            'status' => ['nullable', 'string', 'max:50', Rule::in(['pending', 'running', 'completed', 'failed'])],
            'total_items' => ['nullable', 'integer', 'min:0'],
            'processed_items' => ['nullable', 'integer', 'min:0'],
            'started_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
