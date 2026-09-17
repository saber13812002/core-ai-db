<?php

namespace App\Models;

use Database\Factories\OutputTypeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Reference table describing a kind of output the factory can produce.
 *
 * @property int $id
 * @property string $code
 * @property string $name_fa
 * @property string|null $description
 * @property string|null $output_format
 * @property array<string, mixed>|null $json_schema
 * @property bool $supports_chunk
 * @property bool $is_active
 * @property Carbon $created_at
 */
#[Fillable([
    'code', 'name_fa', 'description', 'output_format',
    'json_schema', 'supports_chunk', 'is_active',
])]
#[Table(name: 'output_types', timestamps: false)]
#[WithoutTimestamps]
class OutputType extends Model
{
    /** @use HasFactory<OutputTypeFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'json_schema' => 'array',
            'supports_chunk' => 'boolean',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    /**
     * @return HasMany<AutomationAction, $this>
     */
    public function automationActions(): HasMany
    {
        return $this->hasMany(AutomationAction::class);
    }

    /**
     * @return HasMany<AutomationFlow, $this>
     */
    public function automationFlows(): HasMany
    {
        return $this->hasMany(AutomationFlow::class);
    }

    /**
     * @return HasMany<MasterPrompt, $this>
     */
    public function prompts(): HasMany
    {
        return $this->hasMany(MasterPrompt::class, 'target_output_type_id');
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
}
