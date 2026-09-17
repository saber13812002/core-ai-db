<?php

namespace App\Models;

use Database\Factories\DatasetItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * A single sample inside a dataset, placed on the train/validation/test split.
 *
 * @property string $id
 * @property string $dataset_id
 * @property string $source_file_id
 * @property string|null $input_output_id
 * @property string|null $input_cleaned_id
 * @property string|null $ground_truth_id
 * @property string $split
 * @property int|null $sequence_order
 * @property array<string, mixed>|null $metadata
 * @property Carbon $created_at
 */
#[Fillable([
    'dataset_id', 'source_file_id', 'input_output_id', 'input_cleaned_id',
    'ground_truth_id', 'split', 'sequence_order', 'metadata',
])]
#[Table(name: 'dataset_items', timestamps: false)]
#[WithoutTimestamps]
class DatasetItem extends Model
{
    /** @use HasFactory<DatasetItemFactory> */
    use HasFactory, HasUuids;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sequence_order' => 'integer',
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Dataset, $this>
     */
    public function dataset(): BelongsTo
    {
        return $this->belongsTo(Dataset::class);
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
    public function inputOutput(): BelongsTo
    {
        return $this->belongsTo(ProcessedOutput::class, 'input_output_id');
    }

    /**
     * @return BelongsTo<CleanedOutput, $this>
     */
    public function inputCleaned(): BelongsTo
    {
        return $this->belongsTo(CleanedOutput::class, 'input_cleaned_id');
    }

    /**
     * @return BelongsTo<HumanGroundTruth, $this>
     */
    public function groundTruth(): BelongsTo
    {
        return $this->belongsTo(HumanGroundTruth::class, 'ground_truth_id');
    }
}
