<?php

namespace App\Services;

use App\Exceptions\FileUploadException;
use App\Exceptions\MetadataSchemaNotFoundException;
use App\Exceptions\MetadataValidationException;
use App\Models\SourceFile;
use App\Repositories\SourceFileRepository;
use App\Support\MetadataSchemaResolver;
use App\Support\MetadataValidator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Result of an upload: the persisted file and whether an identical
 * (same sha256) file already existed, in which case the existing row is
 * returned with $duplicate set.
 */
final class UploadResult
{
    public function __construct(
        public readonly SourceFile $file,
        public readonly bool $duplicate,
    ) {}
}

class FileUploadService extends BaseService
{
    public function __construct(SourceFileRepository $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Store an uploaded binary and register a SourceFile row for it.
     *
     * @param  array<string, mixed>  $options  validated request options: external_ref,
     *                                         language, metadata, metadata_schema,
     *                                         project_id, source_type_id
     * @param  string|null  $creatorId  API key id auto-filled into created_by
     *
     * @throws FileUploadException
     * @throws MetadataSchemaNotFoundException
     * @throws MetadataValidationException
     */
    public function upload(UploadedFile $file, array $options = [], ?string $creatorId = null): UploadResult
    {
        $extension = Str::lower((string) $file->getClientOriginalExtension());
        $allowed = config('ai-factory.upload.allowed');

        if (! array_key_exists($extension, $allowed)) {
            throw FileUploadException::unsupportedExtension();
        }

        if ($file->getSize() === 0) {
            throw FileUploadException::emptyFile();
        }

        if ($file->getSize() > (int) config('ai-factory.upload.max_bytes')) {
            throw FileUploadException::tooLarge();
        }

        // Laravel's getMimeType() returns the client-declared mime for both
        // real uploads and fakes (Symfony's getClientMimeType() would return
        // the extension-guessed type for fakes).
        $declaredMime = $file->getMimeType();

        if ($declaredMime !== '' && ! in_array($declaredMime, $allowed[$extension], true)) {
            throw FileUploadException::mimeMismatch($declaredMime, implode(', ', $allowed[$extension]));
        }

        $metadata = $this->resolveAndValidateMetadata($options['metadata'] ?? null, $options['metadata_schema'] ?? null, $extension);

        $sha256 = hash_file('sha256', $file->getRealPath());

        $existing = $sha256 !== false
            ? $this->repository->findByChecksum($sha256)
            : null;

        if ($existing !== null) {
            return new UploadResult($existing, true);
        }

        $disk = Storage::disk((string) config('ai-factory.upload.disk'));
        $storedName = Str::uuid().'.'.$extension;
        $storagePath = rtrim((string) config('ai-factory.upload.directory'), '/').'/'.$storedName;
        $disk->putFileAs((string) config('ai-factory.upload.directory'), $file, $storedName);

        $sourceFile = $this->repository->create([
            'external_ref' => $options['external_ref'] ?? null,
            'file_type' => $extension,
            'original_filename' => Str::limit($this->sanitizeFilename($file->getClientOriginalName()), 500),
            'storage_path' => $storagePath,
            'mime_type' => $declaredMime !== '' ? $declaredMime : null,
            'file_size_bytes' => $file->getSize(),
            'checksum_sha256' => $sha256,
            'language' => $options['language'] ?? 'fa',
            'metadata' => $metadata,
            'project_id' => $options['project_id'] ?? null,
            'source_type_id' => $options['source_type_id'] ?? null,
            'processing_status' => 'registered',
            'created_by' => $creatorId,
        ]);

        return new UploadResult($sourceFile, false);
    }

    /**
     * Strip path components and control characters from the client filename.
     */
    protected function sanitizeFilename(string $name): string
    {
        $name = basename($name);
        $name = preg_replace('/[\x00-\x1F\x7F]/u', '', $name) ?? $name;

        return $name !== '' ? $name : 'file';
    }

    /**
     * Apply the resolved metadata schema (explicit reference, file-type scope,
     * then global) and reject payloads the schema does not allow.
     *
     * @param  array<string, mixed>|null  $metadata
     * @return array<string, mixed>|null
     *
     * @throws MetadataSchemaNotFoundException
     * @throws MetadataValidationException
     */
    protected function resolveAndValidateMetadata(?array $metadata, ?string $reference, string $fileType): ?array
    {
        if ($metadata === null && ($reference === null || $reference === '')) {
            return null;
        }

        $schema = MetadataSchemaResolver::resolve($reference, $fileType);

        if ($schema === null) {
            if ($reference !== null && $reference !== '') {
                throw MetadataSchemaNotFoundException::for($reference);
            }

            return $metadata;
        }

        $errors = MetadataValidator::validate($metadata ?? [], $schema->schema ?? []);

        if ($errors !== []) {
            throw MetadataValidationException::for($errors);
        }

        return $metadata;
    }
}
