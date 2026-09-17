<?php

namespace App\Http\Resources\Api\V1;

use App\Models\CleanedOutput;
use App\Models\FeedbackLog;
use App\Models\MasterPrompt;
use App\Models\ProcessedOutput;
use App\Models\SourceFile;
use App\Models\TrainedModel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin FeedbackLog
 */
class FeedbackLogResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'source_file_id' => $this->source_file_id,
            'source_file' => $this->whenLoaded('sourceFile', fn (SourceFile $rel): array => [
                'id' => $rel->id,
                'original_filename' => $rel->original_filename,
            ]),
            'processed_output_id' => $this->processed_output_id,
            'processed_output' => $this->whenLoaded('processedOutput', fn (ProcessedOutput $rel): array => [
                'id' => $rel->id,
                'version_number' => $rel->version_number,
            ]),
            'cleaned_output_id' => $this->cleaned_output_id,
            'cleaned_output' => $this->whenLoaded('cleanedOutput', fn (CleanedOutput $rel): array => [
                'id' => $rel->id,
                'version_number' => $rel->version_number,
            ]),
            'trained_model_id' => $this->trained_model_id,
            'trained_model' => $this->whenLoaded('trainedModel', fn (TrainedModel $rel): array => [
                'id' => $rel->id,
                'name' => $rel->name,
                'version' => $rel->version,
            ]),
            'prompt_id' => $this->prompt_id,
            'prompt' => $this->whenLoaded('prompt', fn (MasterPrompt $rel): array => [
                'id' => $rel->id,
                'name' => $rel->name,
            ]),
            'model_id' => $this->model_id,
            'model' => $this->whenLoaded('model', fn ($rel): array => [
                'id' => $rel->id,
                'code' => $rel->code,
            ]),
            'feedback_type' => $this->feedback_type,
            'user_id' => $this->user_id,
            'session_id' => $this->session_id,
            'comment' => $this->comment,
            'context' => $this->context,
            'created_at' => $this->created_at,
        ];
    }
}
