<?php

namespace App\Models;

use Database\Factories\HumanGroundTruthFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Human-validated ground truth for a source file, used as the
 * reference in benchmarks and datasets.
 *
 * @property string $id
 * @property string $source_file_id
 * @property int $output_type_id
 * @property int|null $action_id
 * @property string|null $content_text
 * @property array<string, mixed>|null $content_json
 * @property string|null $storage_path
 * @property string $approved_by
 * @property Carbon $approved_at
 * @property string|null $approval_notes
 * @property string|null $confidence_level
 * @property string|null $based_on_output_id
 * @property string|null $based_on_cleaned_id
 * @property bool $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Fillable([
    'source_file_id', 'output_type_id', 'action_id', 'content_text',
    'content_json', 'storage_path', 'approved_by', 'approved_at',
    'approval_notes', 'confidence_level', 'based_on_output_id',
    'based_on_cleaned_id', 'is_active',
])]
#[Table(name: 'human_ground_truth')]
class HumanGroundTruth extends Model
{
    /** @use HasFactory<HumanGroundTruthFactory> */
    use HasFactory, HasUuids;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'content_json' => 'array',
            'approved_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<SourceFile, $this>
     */
    public function sourceFile(): BelongsTo
    {
        return $this->belongsTo(SourceFile::class);
    }

    /**
     * @return BelongsTo<OutputType, $this>
     */
    public function outputType(): BelongsTo
    {
        return $this->belongsTo(OutputType::class);
    }

    /**
     * @return BelongsTo<AutomationAction, $this>
     */
    public function action(): BelongsTo
    {
        return $this->belongsTo(AutomationAction::class, 'action_id');
    }

    /**
     * @return BelongsTo<ProcessedOutput, $this>
     */
    public function basedOnOutput(): BelongsTo
    {
        return $this->belongsTo(ProcessedOutput::class, 'based_on_output_id');
    }

    /**
     * @return BelongsTo<CleanedOutput, $this>
     */
    public function basedOnCleaned(): BelongsTo
    {
        return $this->belongsTo(CleanedOutput::class, 'based_on_cleaned_id');
    }

    /**
     * @return HasMany<BenchmarkResult, $this>
     */
    public function benchmarkResults(): HasMany
    {
        return $this->hasMany(BenchmarkResult::class);
    }

    /**
     * @return HasMany<DatasetItem, $this>
     */
    public function datasetItems(): HasMany
    {
        return $this->hasMany(DatasetItem::class);
    }
}
