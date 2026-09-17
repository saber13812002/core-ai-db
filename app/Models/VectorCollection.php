<?php

namespace App\Models;

use Database\Factories\VectorCollectionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * A vector database collection (e.g. in ChromaDB) holding chunks for retrieval.
 *
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property string $vector_db
 * @property string|null $external_collection_id
 * @property string $search_mode
 * @property array<string, mixed>|null $filter_criteria
 * @property string $status
 * @property int $total_items
 * @property bool $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Fillable([
    'name', 'description', 'vector_db', 'external_collection_id', 'search_mode',
    'filter_criteria', 'status', 'total_items', 'is_active',
])]
#[Table(name: 'vector_collections')]
class VectorCollection extends Model
{
    /** @use HasFactory<VectorCollectionFactory> */
    use HasFactory, HasUuids;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'filter_criteria' => 'array',
            'total_items' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return HasMany<VectorCollectionItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(VectorCollectionItem::class);
    }
}
