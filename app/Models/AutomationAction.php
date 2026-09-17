<?php

namespace App\Models;

use Database\Factories\AutomationActionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * A processing action the factory can apply to a source file.
 *
 * @property int $id
 * @property string $code
 * @property string $name_fa
 * @property string|null $description
 * @property string|null $action_category
 * @property array<int, string> $input_file_types
 * @property int|null $output_type_id
 * @property bool $requires_prompt
 * @property bool $is_batchable
 * @property bool $is_active
 * @property Carbon $created_at
 */
#[Fillable([
    'code', 'name_fa', 'description', 'action_category',
    'input_file_types', 'output_type_id', 'requires_prompt', 'is_batchable', 'is_active',
])]
#[Table(name: 'automation_actions', timestamps: false)]
#[WithoutTimestamps]
class AutomationAction extends Model
{
    /** @use HasFactory<AutomationActionFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'input_file_types' => 'array',
            'requires_prompt' => 'boolean',
            'is_batchable' => 'boolean',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<OutputType, $this>
     */
    public function outputType(): BelongsTo
    {
        return $this->belongsTo(OutputType::class);
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
}
