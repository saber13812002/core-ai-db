<?php

namespace App\Models;

use Database\Factories\VectorCollectionItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * A single vector entry in a collection, kept traceable to its source file
 * and (optionally) the processed/cleaned output it was chunked from.
 *
 * @property string $id
 * @property string $vector_collection_id
 * @property string $source_file_id
 * @property string|null $processed_output_id
 * @property string|null $cleaned_output_id
 * @property string|null $external_vector_id
 * @property array<string, mixed>|null $chunk_ref
 * @property array<string, mixed>|null $metadata
 * @property Carbon $created_at
 */
#[Fillable([
    'vector_collection_id', 'source_file_id', 'processed_output_id',
    'cleaned_output_id', 'external_vector_id', 'chunk_ref', 'metadata',
])]
#[Table(name: 'vector_collection_items', timestamps: false)]
#[WithoutTimestamps]
class VectorCollectionItem extends Model
{
    /** @use HasFactory<VectorCollectionItemFactory> */
    use HasFactory, HasUuids;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'chunk_ref' => 'array',
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<VectorCollection, $this>
     */
    public function vectorCollection(): BelongsTo
    {
        return $this->belongsTo(VectorCollection::class);
    }

    /**
     * @return BelongsTo<SourceFile, $this>
     */
    public function sourceFile(): BelongsTo
    {
        return $this->belongsTo(SourceFile::class);
    }

    /**
     * @return BelongsTo<ProcessedOutput, $this>
     */
    public function processedOutput(): BelongsTo
    {
        return $this->belongsTo(ProcessedOutput::class, 'processed_output_id');
    }

    /**
     * @return BelongsTo<CleanedOutput, $this>
     */
    public function cleanedOutput(): BelongsTo
    {
        return $this->belongsTo(CleanedOutput::class, 'cleaned_output_id');
    }
}
