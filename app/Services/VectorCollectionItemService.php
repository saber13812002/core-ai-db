<?php

namespace App\Services;

use App\Models\VectorCollectionItem;
use App\Repositories\VectorCollectionItemRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VectorCollectionItemService
{
    public function __construct(protected VectorCollectionItemRepository $repository) {}

    public function listForCollection(string $vectorCollectionId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->listForCollection($vectorCollectionId, $perPage);
    }

    public function createForCollection(string $vectorCollectionId, array $attributes): VectorCollectionItem
    {
        return $this->repository->createForCollection($vectorCollectionId, $attributes);
    }

    public function findInCollection(string $vectorCollectionId, string|int $id): ?VectorCollectionItem
    {
        return $this->repository->findInCollection($vectorCollectionId, $id);
    }

    public function findOrThrowInCollection(string $vectorCollectionId, string|int $id): VectorCollectionItem
    {
        return $this->repository->findOrThrowInCollection($vectorCollectionId, $id);
    }

    public function updateInCollection(string $vectorCollectionId, string|int $id, array $attributes): VectorCollectionItem
    {
        return $this->repository->updateInCollection($vectorCollectionId, $id, $attributes);
    }

    public function deleteInCollection(string $vectorCollectionId, string|int $id): void
    {
        $this->repository->deleteInCollection($vectorCollectionId, $id);
    }
}
