<?php

namespace App\Http\Resources\Api\V1;

use App\Models\CleanedOutput;
use App\Models\Dataset;
use App\Models\DatasetItem;
use App\Models\HumanGroundTruth;
use App\Models\ProcessedOutput;
use App\Models\SourceFile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin DatasetItem
 */
class DatasetItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'dataset_id' => $this->dataset_id,
            'dataset' => $this->whenLoaded('dataset', fn (Dataset $rel): array => [
                'id' => $rel->id,
                'name' => $rel->name,
            ]),
            'source_file_id' => $this->source_file_id,
            'source_file' => $this->whenLoaded('sourceFile', fn (SourceFile $rel): array => [
                'id' => $rel->id,
                'original_filename' => $rel->original_filename,
            ]),
            'input_output_id' => $this->input_output_id,
            'input_output' => $this->whenLoaded('inputOutput', fn (ProcessedOutput $rel): array => [
                'id' => $rel->id,
                'version_number' => $rel->version_number,
            ]),
            'input_cleaned_id' => $this->input_cleaned_id,
            'input_cleaned' => $this->whenLoaded('inputCleaned', fn (CleanedOutput $rel): array => [
                'id' => $rel->id,
                'version_number' => $rel->version_number,
            ]),
            'ground_truth_id' => $this->ground_truth_id,
            'ground_truth' => $this->whenLoaded('groundTruth', fn (HumanGroundTruth $rel): array => [
                'id' => $rel->id,
                'output_type_id' => $rel->output_type_id,
            ]),
            'split' => $this->split,
            'sequence_order' => $this->sequence_order,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
        ];
    }
}
