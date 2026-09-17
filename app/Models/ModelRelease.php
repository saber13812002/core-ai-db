<?php

namespace App\Models;

use Database\Factories\ModelReleaseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * A versioned release of a trained model with an approval gate.
 *
 * @property string $id
 * @property string $trained_model_id
 * @property string $version
 * @property string $status
 * @property string|null $approved_by
 * @property Carbon|null $approved_at
 * @property string|null $rejection_reason
 * @property string|null $release_notes
 * @property array<string, mixed>|null $performance_summary
 * @property Carbon|null $deployed_at
 * @property Carbon|null $retired_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Fillable([
    'trained_model_id', 'version', 'status', 'approved_by', 'approved_at',
    'rejection_reason', 'release_notes', 'performance_summary',
    'deployed_at', 'retired_at',
])]
#[Table(name: 'model_releases')]
class ModelRelease extends Model
{
    /** @use HasFactory<ModelReleaseFactory> */
    use HasFactory, HasUuids;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'performance_summary' => 'array',
            'deployed_at' => 'datetime',
            'retired_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<TrainedModel, $this>
     */
    public function trainedModel(): BelongsTo
    {
        return $this->belongsTo(TrainedModel::class);
    }

    /**
     * @return HasMany<ReleaseReport, $this>
     */
    public function releaseReports(): HasMany
    {
        return $this->hasMany(ReleaseReport::class);
    }
}
