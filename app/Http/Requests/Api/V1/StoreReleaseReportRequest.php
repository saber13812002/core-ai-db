<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreReleaseReportRequest extends FormRequest
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
            'release_id' => ['nullable', 'uuid', 'exists:model_releases,id'],
            'report_type' => ['nullable', 'string', 'max:50'],
            'content' => ['required', 'array'],
            'storage_path' => ['nullable', 'string'],
        ];
    }
}
