<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMasterPromptRequest extends FormRequest
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
            'family_id' => ['required', 'uuid'],
            'name' => ['required', 'string', 'max:300'],
            'version' => ['required', 'string', 'max:50'],
            'content' => ['required', 'string'],
            'content_hash' => ['required', 'string', 'max:64'],
            'prompt_type' => ['required', 'string', 'max:50', Rule::in(['extraction', 'summarization', 'generation', 'cleaning', 'judging'])],
            'purpose' => ['nullable', 'string'],
            'target_output_type_id' => ['nullable', 'integer', 'exists:output_types,id'],
            'target_action_id' => ['nullable', 'integer', 'exists:automation_actions,id'],
            'parent_prompt_id' => ['nullable', 'uuid', 'exists:prompts,id'],
            'is_active' => ['boolean'],
            'tags' => ['nullable', 'array'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
