<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UploadSourceFileRequest extends FormRequest
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
        // Extension (415 unsupported_file_type) and size (413 file_too_large)
        // are enforced by the service so the documented status codes and
        // error codes are returned.
        return [
            'file' => ['required', 'file'],
            'external_ref' => ['nullable', 'string', 'max:100', 'unique:source_files,external_ref'],
            'language' => ['nullable', 'string', 'max:10'],
            'metadata' => ['nullable', 'array'],
            'metadata_schema' => ['nullable', 'string', 'max:200'],
            'project_id' => ['nullable', 'uuid', 'exists:projects,id'],
            'source_type_id' => ['nullable', 'integer', 'exists:source_types,id'],
        ];
    }
}
