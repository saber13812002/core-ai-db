<?php

namespace App\Models;

use Database\Factories\AiModelFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * A registered AI model (provider, endpoint, context window).
 * Named AiModel to avoid colliding with the Eloquent base Model class.
 *
 * @property int $id
 * @property string $code
 * @property string|null $name_fa
 * @property string|null $provider
 * @property string|null $model_type
 * @property string|null $version
 * @property string|null $endpoint
 * @property int|null $context_window
 * @property bool $is_active
 * @property array<string, mixed>|null $metadata
 * @property Carbon $created_at
 */
#[Fillable([
    'code', 'name_fa', 'provider', 'model_type', 'version',
    'endpoint', 'context_window', 'is_active', 'metadata',
])]
#[Table(name: 'models', timestamps: false)]
#[WithoutTimestamps]
class AiModel extends Model
{
    /** @use HasFactory<AiModelFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
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
     * @return HasMany<BenchmarkSession, $this>
     */
    public function benchmarkSessions(): HasMany
    {
        return $this->hasMany(BenchmarkSession::class, 'judge_model_id');
    }

    /**
     * @return HasMany<BenchmarkResult, $this>
     */
    public function benchmarkResults(): HasMany
    {
        return $this->hasMany(BenchmarkResult::class, 'judge_model_id');
    }

    /**
     * @return HasMany<ModelEvaluation, $this>
     */
    public function modelEvaluations(): HasMany
    {
        return $this->hasMany(ModelEvaluation::class, 'judge_model_id');
    }

    /**
     * @return HasMany<FeedbackLog, $this>
     */
    public function feedbackLogs(): HasMany
    {
        return $this->hasMany(FeedbackLog::class);
    }
}
