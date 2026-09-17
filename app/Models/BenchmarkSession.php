<?php

namespace App\Models;

use Database\Factories\BenchmarkSessionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * A benchmark run comparing candidate vs baseline outputs.
 *
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property string $benchmark_type
 * @property string|null $source_file_id
 * @property int|null $output_type_id
 * @property int|null $action_id
 * @property int|null $judge_model_id
 * @property string|null $judge_prompt_id
 * @property string $status
 * @property int|null $total_items
 * @property int $processed_items
 * @property Carbon|null $started_at
 * @property Carbon|null $completed_at
 * @property array<string, mixed>|null $metadata
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Fillable([
    'name', 'description', 'benchmark_type', 'source_file_id', 'output_type_id',
    'action_id', 'judge_model_id', 'judge_prompt_id', 'status', 'total_items',
    'processed_items', 'started_at', 'completed_at', 'metadata',
])]
#[Table(name: 'benchmark_sessions')]
class BenchmarkSession extends Model
{
    /** @use HasFactory<BenchmarkSessionFactory> */
    use HasFactory, HasUuids;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_items' => 'integer',
            'processed_items' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'metadata' => 'array',
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
     * @return BelongsTo<AiModel, $this>
     */
    public function judgeModel(): BelongsTo
    {
        return $this->belongsTo(AiModel::class, 'judge_model_id');
    }

    /**
     * @return BelongsTo<MasterPrompt, $this>
     */
    public function judgePrompt(): BelongsTo
    {
        return $this->belongsTo(MasterPrompt::class, 'judge_prompt_id');
    }

    /**
     * @return HasMany<BenchmarkResult, $this>
     */
    public function benchmarkResults(): HasMany
    {
        return $this->hasMany(BenchmarkResult::class);
    }

    /**
     * @return HasMany<ModelEvaluation, $this>
     */
    public function modelEvaluations(): HasMany
    {
        return $this->hasMany(ModelEvaluation::class);
    }
}
