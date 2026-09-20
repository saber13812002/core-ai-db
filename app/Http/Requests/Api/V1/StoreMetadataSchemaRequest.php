<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreMetadataSchemaRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:200', 'unique:metadata_schemas,name'],
            'scope' => ['required', 'string', 'max:50'],
            'schema' => ['required', 'array'],
            'schema.*.type' => ['required', 'string', 'in:string,integer,boolean,object'],
            'schema.*.required' => ['boolean'],
            'schema.*.enum' => ['nullable', 'array'],
            'schema.*.additional' => ['nullable', 'string', 'in:allow,reject'],
            'is_active' => ['boolean'],
        ];
    }
}
