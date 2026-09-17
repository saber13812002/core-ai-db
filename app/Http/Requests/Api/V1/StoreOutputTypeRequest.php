<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreOutputTypeRequest extends FormRequest
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
            'code' => ['required', 'string', 'max:80', 'unique:output_types,code'],
            'name_fa' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'output_format' => ['nullable', 'string', 'max:50'],
            'json_schema' => ['nullable', 'array'],
            'supports_chunk' => ['boolean'],
            'is_active' => ['boolean'],
        ];
    }
}
