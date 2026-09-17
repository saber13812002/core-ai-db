<?php

namespace App\Http\Resources\Api\V1;

use App\Models\AutomationAction;
use App\Models\BenchmarkSession;
use App\Models\MasterPrompt;
use App\Models\OutputType;
use App\Models\SourceFile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin BenchmarkSession
 */
class BenchmarkSessionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'benchmark_type' => $this->benchmark_type,
            'source_file_id' => $this->source_file_id,
            'source_file' => $this->whenLoaded('sourceFile', fn (SourceFile $rel): array => [
                'id' => $rel->id,
                'original_filename' => $rel->original_filename,
            ]),
            'output_type_id' => $this->output_type_id,
            'output_type' => $this->whenLoaded('outputType', fn (OutputType $rel): array => [
                'id' => $rel->id,
                'code' => $rel->code,
            ]),
            'action_id' => $this->action_id,
            'action' => $this->whenLoaded('action', fn (AutomationAction $rel): array => [
                'id' => $rel->id,
                'code' => $rel->code,
            ]),
            'judge_model_id' => $this->judge_model_id,
            'judge_model' => $this->whenLoaded('judgeModel', fn ($rel): array => [
                'id' => $rel->id,
                'code' => $rel->code,
            ]),
            'judge_prompt_id' => $this->judge_prompt_id,
            'judge_prompt' => $this->whenLoaded('judgePrompt', fn (MasterPrompt $rel): array => [
                'id' => $rel->id,
                'name' => $rel->name,
            ]),
            'status' => $this->status,
            'total_items' => $this->total_items,
            'processed_items' => $this->processed_items,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
