<?php

namespace App\Http\Resources\Api\V1;

use App\Models\OutputType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin OutputType
 */
class OutputTypeResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name_fa' => $this->name_fa,
            'description' => $this->description,
            'output_format' => $this->output_format,
            'json_schema' => $this->json_schema,
            'supports_chunk' => $this->supports_chunk,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
        ];
    }
}
