<?php

namespace App\Models;

use Database\Factories\TrainedModelFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * An artifact produced by a training job. Uniquely identified by name + version.
 *
 * @property string $id
 * @property string|null $training_job_id
 * @property string $name
 * @property string $version
 * @property string|null $model_type
 * @property string|null $base_model_code
 * @property string|null $storage_path
 * @property string|null $service_endpoint
 * @property string $status
 * @property array<string, mixed>|null $metadata
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Fillable([
    'training_job_id', 'name', 'version', 'model_type', 'base_model_code',
    'storage_path', 'service_endpoint', 'status', 'metadata',
])]
#[Table(name: 'trained_models')]
class TrainedModel extends Model
{
    /** @use HasFactory<TrainedModelFactory> */
    use HasFactory, HasUuids;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    /**
     * @return BelongsTo<TrainingJob, $this>
     */
    public function trainingJob(): BelongsTo
    {
        return $this->belongsTo(TrainingJob::class);
    }

    /**
     * @return HasMany<ModelEvaluation, $this>
     */
    public function modelEvaluations(): HasMany
    {
        return $this->hasMany(ModelEvaluation::class);
    }

    /**
     * @return HasMany<ModelEvaluation, $this>
     */
    public function baselineEvaluations(): HasMany
    {
        return $this->hasMany(ModelEvaluation::class, 'baseline_model_id');
    }

    /**
     * @return HasMany<ModelRelease, $this>
     */
    public function releases(): HasMany
    {
        return $this->hasMany(ModelRelease::class);
    }

    /**
     * @return HasMany<FeedbackLog, $this>
     */
    public function feedbackLogs(): HasMany
    {
        return $this->hasMany(FeedbackLog::class);
    }
}
