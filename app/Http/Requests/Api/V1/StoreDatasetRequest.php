<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDatasetRequest extends FormRequest
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
            'purpose' => ['required', 'string', 'max:50'],
            'target_model_type' => ['nullable', 'string', 'max:50'],
            'target_output_type_id' => ['nullable', 'integer', 'exists:output_types,id'],
            'filter_criteria' => ['nullable', 'array'],
            'status' => ['nullable', 'string', 'max:50', Rule::in(['building', 'ready', 'archived'])],
            'version_number' => ['nullable', 'integer', 'min:1'],
            'previous_dataset_id' => ['nullable', 'uuid', 'exists:datasets,id'],
            'total_items' => ['nullable', 'integer', 'min:0'],
            'train_count' => ['nullable', 'integer', 'min:0'],
            'validation_count' => ['nullable', 'integer', 'min:0'],
            'test_count' => ['nullable', 'integer', 'min:0'],
            'storage_path' => ['nullable', 'string'],
            'created_by' => ['nullable', 'uuid'],
        ];
    }
}
