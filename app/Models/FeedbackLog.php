<?php

namespace App\Models;

use Database\Factories\FeedbackLogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * User feedback tied to a prompt-model pair per project rules.
 *
 * @property int $id
 * @property string|null $source_file_id
 * @property string|null $processed_output_id
 * @property string|null $cleaned_output_id
 * @property string|null $trained_model_id
 * @property string|null $prompt_id
 * @property int|null $model_id
 * @property string $feedback_type
 * @property string|null $user_id
 * @property string|null $session_id
 * @property string|null $comment
 * @property array<string, mixed>|null $context
 * @property Carbon $created_at
 */
#[Fillable([
    'source_file_id', 'processed_output_id', 'cleaned_output_id',
    'trained_model_id', 'prompt_id', 'model_id', 'feedback_type',
    'user_id', 'session_id', 'comment', 'context',
])]
#[Table(name: 'feedback_logs', timestamps: false)]
#[WithoutTimestamps]
class FeedbackLog extends Model
{
    /** @use HasFactory<FeedbackLogFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'context' => 'array',
            'created_at' => 'datetime',
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
     * @return BelongsTo<ProcessedOutput, $this>
     */
    public function processedOutput(): BelongsTo
    {
        return $this->belongsTo(ProcessedOutput::class);
    }

    /**
     * @return BelongsTo<CleanedOutput, $this>
     */
    public function cleanedOutput(): BelongsTo
    {
        return $this->belongsTo(CleanedOutput::class);
    }

    /**
     * @return BelongsTo<TrainedModel, $this>
     */
    public function trainedModel(): BelongsTo
    {
        return $this->belongsTo(TrainedModel::class);
    }

    /**
     * @return BelongsTo<MasterPrompt, $this>
     */
    public function prompt(): BelongsTo
    {
        return $this->belongsTo(MasterPrompt::class, 'prompt_id');
    }

    /**
     * @return BelongsTo<AiModel, $this>
     */
    public function model(): BelongsTo
    {
        return $this->belongsTo(AiModel::class, 'model_id');
    }
}
