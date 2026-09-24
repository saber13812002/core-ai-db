<?php

namespace App\Repositories;

use App\Models\DatasetItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class DatasetItemRepository extends BaseRepository
{
    public function __construct(DatasetItem $model)
    {
        parent::__construct($model);
    }

    protected function orderColumn(): ?string
    {
        // created_at-only table: UUID ids have no insertion order.
        return null;
    }

    /**
     * Paginated list filtered by dataset and/or split.
     */
    public function listWithFilters(
        ?string $datasetId = null,
        ?string $split = null,
        int $perPage = 15,
    ): LengthAwarePaginator {
        return $this->query()
            ->when($datasetId !== null, fn (Builder $query) => $query->where('dataset_id', $datasetId))
            ->when($split !== null && $split !== '', fn (Builder $query) => $query->where('split', $split))
            ->paginate($perPage);
    }
}
