<?php

namespace App\Models;

use Database\Factories\DatasetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * A curated training dataset assembled from outputs and ground truth.
 *
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property string $purpose
 * @property string|null $target_model_type
 * @property int|null $target_output_type_id
 * @property array<string, mixed>|null $filter_criteria
 * @property string $status
 * @property int $version_number
 * @property string|null $previous_dataset_id
 * @property int $total_items
 * @property int $train_count
 * @property int $validation_count
 * @property int $test_count
 * @property string|null $storage_path
 * @property string|null $created_by
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Fillable([
    'name', 'description', 'purpose', 'target_model_type', 'target_output_type_id',
    'filter_criteria', 'status', 'version_number', 'previous_dataset_id',
    'total_items', 'train_count', 'validation_count',
    'test_count', 'storage_path', 'created_by',
])]
#[Table(name: 'datasets')]
class Dataset extends Model
{
    /** @use HasFactory<DatasetFactory> */
    use HasFactory, HasUuids;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'filter_criteria' => 'array',
            'version_number' => 'integer',
            'total_items' => 'integer',
            'train_count' => 'integer',
            'validation_count' => 'integer',
            'test_count' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<OutputType, $this>
     */
    public function targetOutputType(): BelongsTo
    {
        return $this->belongsTo(OutputType::class, 'target_output_type_id');
    }

    /**
     * @return BelongsTo<Dataset, $this>
     */
    public function previousDataset(): BelongsTo
    {
        return $this->belongsTo(Dataset::class, 'previous_dataset_id');
    }

    /**
     * @return HasMany<DatasetItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(DatasetItem::class);
    }

    /**
     * @return HasMany<TrainingJob, $this>
     */
    public function trainingJobs(): HasMany
    {
        return $this->hasMany(TrainingJob::class);
    }
}
