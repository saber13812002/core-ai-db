<?php

namespace App\Models;

use Database\Factories\SourceFileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * An immutable source document. Identity metadata (instructor, topic,
 * chapter/session, document type, ...) lives in the JSONB metadata column.
 *
 * @property string $id
 * @property string|null $external_ref
 * @property string|null $project_id
 * @property string $file_type
 * @property int|null $source_type_id
 * @property string|null $original_filename
 * @property string $storage_path
 * @property string|null $mime_type
 * @property int|null $file_size_bytes
 * @property string|null $checksum_sha256
 * @property int|null $duration_seconds
 * @property int|null $page_count
 * @property string $language
 * @property array<string, mixed>|null $metadata
 * @property string|null $metadata_schema_id
 * @property bool $human_approved
 * @property string|null $human_approved_by
 * @property Carbon|null $human_approved_at
 * @property string|null $human_approval_note
 * @property string $processing_status
 * @property int $version_number
 * @property string|null $superseded_by_id
 * @property bool $is_latest
 * @property string|null $created_by
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable([
    'external_ref', 'project_id', 'file_type', 'source_type_id', 'original_filename', 'storage_path', 'mime_type',
    'file_size_bytes', 'checksum_sha256', 'duration_seconds', 'page_count', 'language',
    'metadata', 'metadata_schema_id', 'human_approved', 'human_approved_by', 'human_approved_at', 'human_approval_note',
    'processing_status', 'version_number', 'superseded_by_id', 'is_latest', 'created_by',
])]
#[Table(name: 'source_files')]
class SourceFile extends Model
{
    /** @use HasFactory<SourceFileFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'file_size_bytes' => 'integer',
            'metadata' => 'array',
            'human_approved' => 'boolean',
            'human_approved_at' => 'datetime',
            'version_number' => 'integer',
            'is_latest' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return BelongsTo<SourceType, $this>
     */
    public function sourceType(): BelongsTo
    {
        return $this->belongsTo(SourceType::class);
    }

    /**
     * @return BelongsTo<SourceFile, $this>
     */
    public function supersededBy(): BelongsTo
    {
        return $this->belongsTo(SourceFile::class, 'superseded_by_id');
    }

    /**
     * @return BelongsTo<MetadataSchema, $this>
     */
    public function metadataSchema(): BelongsTo
    {
        return $this->belongsTo(MetadataSchema::class, 'metadata_schema_id');
    }

    /**
     * @return HasMany<AutomationJob, $this>
     */
    public function automationJobs(): HasMany
    {
        return $this->hasMany(AutomationJob::class);
    }

    /**
     * @return HasMany<ProcessedOutput, $this>
     */
    public function processedOutputs(): HasMany
    {
        return $this->hasMany(ProcessedOutput::class);
    }

    /**
     * @return HasMany<CleanedOutput, $this>
     */
    public function cleanedOutputs(): HasMany
    {
        return $this->hasMany(CleanedOutput::class);
    }

    /**
     * @return HasMany<HumanGroundTruth, $this>
     */
    public function humanGroundTruths(): HasMany
    {
        return $this->hasMany(HumanGroundTruth::class);
    }

    /**
     * @return HasMany<BenchmarkSession, $this>
     */
    public function benchmarkSessions(): HasMany
    {
        return $this->hasMany(BenchmarkSession::class);
    }

    /**
     * @return HasMany<DatasetItem, $this>
     */
    public function datasetItems(): HasMany
    {
        return $this->hasMany(DatasetItem::class);
    }

    /**
     * @return HasMany<VectorCollectionItem, $this>
     */
    public function vectorCollectionItems(): HasMany
    {
        return $this->hasMany(VectorCollectionItem::class);
    }

    /**
     * @return HasMany<FeedbackLog, $this>
     */
    public function feedbackLogs(): HasMany
    {
        return $this->hasMany(FeedbackLog::class);
    }

    /**
     * @return HasMany<ServiceCallLog, $this>
     */
    public function serviceCallLogs(): HasMany
    {
        return $this->hasMany(ServiceCallLog::class);
    }
}
