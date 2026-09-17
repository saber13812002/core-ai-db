<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreAutomationActionRequest extends FormRequest
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
            'code' => ['required', 'string', 'max:80', 'unique:automation_actions,code'],
            'name_fa' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'action_category' => ['nullable', 'string', 'max:50'],
            'input_file_types' => ['required', 'array'],
            'output_type_id' => ['nullable', 'integer', 'exists:output_types,id'],
            'requires_prompt' => ['boolean'],
            'is_batchable' => ['boolean'],
            'is_active' => ['boolean'],
        ];
    }
}
