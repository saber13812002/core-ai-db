<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreAutomationFlowRequest extends FormRequest
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
            'platform' => ['required', 'string', 'max:50'],
            'platform_flow_id' => ['nullable', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'input_file_types' => ['nullable', 'array'],
            'output_type_id' => ['nullable', 'integer', 'exists:output_types,id'],
            'action_id' => ['nullable', 'integer', 'exists:automation_actions,id'],
            'config' => ['nullable', 'array'],
            'is_active' => ['boolean'],
        ];
    }
}
