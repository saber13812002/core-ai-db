<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDatasetItemRequest extends FormRequest
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
            'dataset_id' => ['required', 'uuid', 'exists:datasets,id'],
            'source_file_id' => ['required', 'uuid', 'exists:source_files,id'],
            'input_output_id' => ['nullable', 'uuid', 'exists:processed_outputs,id'],
            'input_cleaned_id' => ['nullable', 'uuid', 'exists:cleaned_outputs,id'],
            'ground_truth_id' => ['nullable', 'uuid', 'exists:human_ground_truth,id'],
            'split' => ['required', 'string', 'max:20', Rule::in(['train', 'validation', 'test'])],
            'sequence_order' => ['nullable', 'integer'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
