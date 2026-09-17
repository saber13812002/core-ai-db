<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Dataset;
use App\Models\OutputType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Dataset
 */
class DatasetResource extends JsonResource
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
            'purpose' => $this->purpose,
            'target_model_type' => $this->target_model_type,
            'target_output_type_id' => $this->target_output_type_id,
            'target_output_type' => $this->whenLoaded('targetOutputType', fn (OutputType $rel): array => [
                'id' => $rel->id,
                'code' => $rel->code,
                'name_fa' => $rel->name_fa,
            ]),
            'filter_criteria' => $this->filter_criteria,
            'status' => $this->status,
            'total_items' => $this->total_items,
            'train_count' => $this->train_count,
            'validation_count' => $this->validation_count,
            'test_count' => $this->test_count,
            'storage_path' => $this->storage_path,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
