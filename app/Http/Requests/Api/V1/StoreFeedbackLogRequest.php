<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFeedbackLogRequest extends FormRequest
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
            'source_file_id' => ['nullable', 'uuid', 'exists:source_files,id'],
            'processed_output_id' => ['nullable', 'uuid', 'exists:processed_outputs,id'],
            'cleaned_output_id' => ['nullable', 'uuid', 'exists:cleaned_outputs,id'],
            'trained_model_id' => ['nullable', 'uuid', 'exists:trained_models,id'],
            'prompt_id' => ['nullable', 'uuid', 'exists:prompts,id'],
            'model_id' => ['nullable', 'integer', 'exists:models,id'],
            'feedback_type' => ['required', 'string', 'max:20', Rule::in(['like', 'dislike', 'correction', 'flag'])],
            'user_id' => ['nullable', 'string', 'max:200'],
            'session_id' => ['nullable', 'string', 'max:200'],
            'comment' => ['nullable', 'string'],
            'context' => ['nullable', 'array'],
        ];
    }
}
