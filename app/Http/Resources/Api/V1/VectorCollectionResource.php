<?php

namespace App\Http\Resources\Api\V1;

use App\Models\VectorCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin VectorCollection
 */
class VectorCollectionResource extends JsonResource
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
            'vector_db' => $this->vector_db,
            'external_collection_id' => $this->external_collection_id,
            'search_mode' => $this->search_mode,
            'filter_criteria' => $this->filter_criteria,
            'status' => $this->status,
            'total_items' => $this->total_items,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
