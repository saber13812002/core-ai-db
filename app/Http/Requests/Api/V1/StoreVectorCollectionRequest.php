<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVectorCollectionRequest extends FormRequest
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
            'vector_db' => ['nullable', 'string', 'max:50', Rule::in(['chromadb', 'pgvector', 'qdrant'])],
            'external_collection_id' => ['nullable', 'string', 'max:200'],
            'search_mode' => ['nullable', 'string', 'max:50', Rule::in(['lexical', 'semantic', 'hybrid'])],
            'filter_criteria' => ['nullable', 'array'],
            'status' => ['nullable', 'string', 'max:50', Rule::in(['building', 'ready', 'archived'])],
            'total_items' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }
}
