<?php

namespace App\Models;

use Database\Factories\AutomationFlowFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * An external platform flow that orchestrates actions.
 *
 * @property string $id
 * @property string $name
 * @property string $platform
 * @property string|null $platform_flow_id
 * @property string|null $description
 * @property array<int, string>|null $input_file_types
 * @property int|null $output_type_id
 * @property int|null $action_id
 * @property array<string, mixed>|null $config
 * @property bool $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Fillable([
    'name', 'platform', 'platform_flow_id', 'description',
    'input_file_types', 'output_type_id', 'action_id', 'config', 'is_active',
])]
#[Table(name: 'automation_flows')]
class AutomationFlow extends Model
{
    /** @use HasFactory<AutomationFlowFactory> */
    use HasFactory, HasUuids;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'input_file_types' => 'array',
            'config' => 'array',
            'is_active' => 'boolean',
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
     * @return BelongsTo<AutomationAction, $this>
     */
    public function action(): BelongsTo
    {
        return $this->belongsTo(AutomationAction::class, 'action_id');
    }

    /**
     * @return HasMany<AutomationJob, $this>
     */
    public function automationJobs(): HasMany
    {
        return $this->hasMany(AutomationJob::class);
    }
}
