<?php

namespace App\Repositories;

use App\Models\VectorCollectionItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VectorCollectionItemRepository extends BaseRepository
{
    public function __construct(VectorCollectionItem $model)
    {
        parent::__construct($model);
    }

    protected function orderColumn(): ?string
    {
        // created_at-only table.
        return null;
    }

    public function listForCollection(string $vectorCollectionId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->query()
            ->where('vector_collection_id', $vectorCollectionId)
            ->paginate($perPage);
    }

    public function createForCollection(string $vectorCollectionId, array $attributes): VectorCollectionItem
    {
        return $this->query()->create(['vector_collection_id' => $vectorCollectionId, ...$attributes]);
    }

    public function findInCollection(string $vectorCollectionId, string|int $id): ?VectorCollectionItem
    {
        return $this->query()
            ->where('vector_collection_id', $vectorCollectionId)
            ->whereKey($id)
            ->first();
    }

    public function findOrThrowInCollection(string $vectorCollectionId, string|int $id): VectorCollectionItem
    {
        return $this->query()
            ->where('vector_collection_id', $vectorCollectionId)
            ->whereKey($id)
            ->firstOrFail();
    }

    public function updateInCollection(string $vectorCollectionId, string|int $id, array $attributes): VectorCollectionItem
    {
        $record = $this->findOrThrowInCollection($vectorCollectionId, $id);

        $record->update($attributes);

        return $record;
    }

    public function deleteInCollection(string $vectorCollectionId, string|int $id): void
    {
        $this->findOrThrowInCollection($vectorCollectionId, $id)->delete();
    }
}
