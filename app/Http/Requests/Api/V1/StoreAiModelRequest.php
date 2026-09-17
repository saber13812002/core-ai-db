<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreAiModelRequest extends FormRequest
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
            'code' => ['required', 'string', 'max:120', 'unique:models,code'],
            'name_fa' => ['nullable', 'string', 'max:200'],
            'provider' => ['nullable', 'string', 'max:100'],
            'model_type' => ['nullable', 'string', 'max:50'],
            'version' => ['nullable', 'string', 'max:50'],
            'endpoint' => ['nullable', 'string'],
            'context_window' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['boolean'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
