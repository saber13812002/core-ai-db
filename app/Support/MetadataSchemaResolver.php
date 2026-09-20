<?php

namespace App\Support;

use App\Models\MetadataSchema;
use Illuminate\Support\Str;

/**
 * Resolves which active MetadataSchema applies to a source file's metadata.
 *
 * Precedence:
 *   1. Explicit reference by name or UUID (metadata_schema / metadata_schema_id).
 *   2. The active schema scoped to the file's file_type.
 *   3. The active schema scoped to "global".
 *   4. null — no schema is applied and metadata is accepted as-is.
 */
class MetadataSchemaResolver
{
    /**
     * @param  string|null  $reference  Explicit schema name or UUID, or null.
     */
    public static function resolve(?string $reference, ?string $fileType = null): ?MetadataSchema
    {
        if ($reference !== null && $reference !== '') {
            $schema = Str::isUuid($reference)
                ? MetadataSchema::whereKey($reference)->first()
                : MetadataSchema::where('name', $reference)->first();

            if ($schema !== null) {
                return $schema->is_active ? $schema : null;
            }
        }

        if ($fileType !== null && $fileType !== '') {
            $scoped = MetadataSchema::query()
                ->where('scope', $fileType)
                ->where('is_active', true)
                ->first();

            if ($scoped !== null) {
                return $scoped;
            }
        }

        return MetadataSchema::query()
            ->where('scope', 'global')
            ->where('is_active', true)
            ->first();
    }
}
