<?php

namespace App\Http\Requests\Api\V1;

use App\Support\MetadataSchemaResolver;
use App\Support\MetadataValidator;
use Closure;
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
            'metadata' => ['nullable', 'array', $this->schemaValidationRule()],
            'metadata_schema_id' => ['nullable', 'uuid', 'exists:metadata_schemas,id'],
            'project_id' => ['nullable', 'uuid', 'exists:projects,id'],
            'source_type_id' => ['nullable', 'integer', 'exists:source_types,id'],
            'human_approved' => ['boolean'],
            'human_approved_by' => ['nullable', 'uuid'],
            'human_approved_at' => ['nullable', 'date'],
            'human_approval_note' => ['nullable', 'string'],
            'processing_status' => ['nullable', 'string', 'max:50', 'in:pending,registered,processing,completed,failed'],
            'version_number' => ['nullable', 'integer', 'min:1'],
            'superseded_by_id' => ['nullable', 'uuid', 'exists:source_files,id'],
            'is_latest' => ['boolean'],
            'created_by' => ['nullable', 'uuid'],
        ];
    }

    /**
     * Closure rule enforcing the resolved metadata schema (explicit
     * metadata_schema_id, file-type scope, then global) over the payload,
     * so violations surface as standard 422 validation errors on `metadata`.
     */
    protected function schemaValidationRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if (! is_array($value)) {
                return;
            }

            $fileType = $this->input('file_type') ?? $this->route('file')?->file_type;

            $schema = MetadataSchemaResolver::resolve($this->input('metadata_schema_id'), $fileType);

            if ($schema === null) {
                return;
            }

            $errors = MetadataValidator::validate($value, $schema->schema ?? []);

            foreach ($errors as $error) {
                $fail($error);
            }
        };
    }
}
