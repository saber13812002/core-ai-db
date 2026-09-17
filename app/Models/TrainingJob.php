<?php

namespace App\Models;

use Database\Factories\TrainingJobFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * A fine-tuning run against a dataset on an external training service.
 *
 * @property string $id
 * @property string $dataset_id
 * @property int|null $service_id
 * @property string|null $external_job_id
 * @property string|null $base_model_code
 * @property array<string, mixed>|null $training_config
 * @property string $status
 * @property int $progress_percent
 * @property float|null $estimated_cost_usd
 * @property float|null $actual_cost_usd
 * @property string|null $error_message
 * @property Carbon|null $started_at
 * @property Carbon|null $completed_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Fillable([
    'dataset_id', 'service_id', 'external_job_id', 'base_model_code',
    'training_config', 'status', 'progress_percent', 'estimated_cost_usd',
    'actual_cost_usd', 'error_message', 'started_at', 'completed_at',
])]
#[Table(name: 'training_jobs')]
class TrainingJob extends Model
{
    /** @use HasFactory<TrainingJobFactory> */
    use HasFactory, HasUuids;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'training_config' => 'array',
            'progress_percent' => 'integer',
            'estimated_cost_usd' => 'decimal:4',
            'actual_cost_usd' => 'decimal:4',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
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
     * @return BelongsTo<ServiceRegistry, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(ServiceRegistry::class, 'service_id');
    }

    /**
     * @return HasMany<TrainedModel, $this>
     */
    public function trainedModels(): HasMany
    {
        return $this->hasMany(TrainedModel::class);
    }
}
