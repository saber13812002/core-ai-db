<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreVectorCollectionItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     *
     * Note: vector_collection_id is bound from the route, not the payload.
     */
    public function rules(): array
    {
        return [
            'source_file_id' => ['required', 'uuid', 'exists:source_files,id'],
            'processed_output_id' => ['nullable', 'uuid', 'exists:processed_outputs,id'],
            'cleaned_output_id' => ['nullable', 'uuid', 'exists:cleaned_outputs,id'],
            'external_vector_id' => ['nullable', 'string', 'max:200'],
            'chunk_ref' => ['nullable', 'array'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
