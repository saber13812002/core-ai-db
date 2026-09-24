<?php

namespace App\Http\Resources\Api\V1;

use App\Models\SourceType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SourceType
 */
class SourceTypeResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'label_fa' => $this->label_fa,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
        ];
    }
}
