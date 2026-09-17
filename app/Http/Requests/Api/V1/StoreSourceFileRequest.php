<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreSourceFileRequest extends FormRequest
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
            'external_ref' => ['nullable', 'string', 'max:100', 'unique:source_files,external_ref'],
            'file_type' => ['required', 'string', 'max:50'],
            'original_filename' => ['nullable', 'string', 'max:500'],
            'storage_path' => ['required', 'string'],
            'mime_type' => ['nullable', 'string', 'max:100'],
            'file_size_bytes' => ['nullable', 'integer', 'min:0'],
            'checksum_sha256' => ['nullable', 'string', 'max:64'],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
            'page_count' => ['nullable', 'integer', 'min:0'],
            'language' => ['nullable', 'string', 'max:10'],
            'metadata' => ['nullable', 'array'],
            'human_approved' => ['boolean'],
            'human_approved_by' => ['nullable', 'uuid'],
            'human_approved_at' => ['nullable', 'date'],
            'human_approval_note' => ['nullable', 'string'],
            'processing_status' => ['nullable', 'string', 'max:50', 'in:pending,processing,completed,failed'],
            'version_number' => ['nullable', 'integer', 'min:1'],
            'superseded_by_id' => ['nullable', 'uuid', 'exists:source_files,id'],
            'is_latest' => ['boolean'],
            'created_by' => ['nullable', 'uuid'],
        ];
    }
}
