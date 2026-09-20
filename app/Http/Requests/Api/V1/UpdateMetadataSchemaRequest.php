<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMetadataSchemaRequest extends FormRequest
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
            'name' => ['sometimes', 'required', 'string', 'max:200', Rule::unique('metadata_schemas', 'name')->ignore($this->route('metadataSchema'))],
            'scope' => ['sometimes', 'required', 'string', 'max:50'],
            'schema' => ['sometimes', 'required', 'array'],
            'schema.*.type' => ['required', 'string', 'in:string,integer,boolean,object'],
            'schema.*.required' => ['boolean'],
            'schema.*.enum' => ['nullable', 'array'],
            'schema.*.additional' => ['nullable', 'string', 'in:allow,reject'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
