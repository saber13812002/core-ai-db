<?php

namespace App\Repositories;

use App\Models\JobBatch;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class JobBatchRepository extends BaseRepository
{
    public function __construct(JobBatch $model)
    {
        parent::__construct($model);
    }

    /**
     * Paginated list filtered by status and/or the project of the files
     * whose jobs belong to the batch.
     */
    public function listWithFilters(?string $status = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->query()
            ->when($status !== null && $status !== '', fn (Builder $query) => $query->where('status', $status))
            ->latest('created_at')
            ->paginate($perPage);
    }
}
