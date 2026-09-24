<?php

namespace App\Repositories;

use App\Models\AutomationJob;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class AutomationJobRepository extends BaseRepository
{
    public function __construct(AutomationJob $model)
    {
        parent::__construct($model);
    }

    /**
     * Paginated list filtered by status, origin source, automation flag,
     * source file and batch.
     */
    public function listWithFilters(
        ?string $status = null,
        ?string $source = null,
        ?bool $isAutomatic = null,
        ?string $sourceFileId = null,
        ?string $batchId = null,
        int $perPage = 15,
    ): LengthAwarePaginator {
        return $this->query()
            ->when($status !== null && $status !== '', fn (Builder $query) => $query->where('status', $status))
            ->when($source !== null && $source !== '', fn (Builder $query) => $query->where('source', $source))
            ->when($isAutomatic !== null, fn (Builder $query) => $query->where('is_automatic', $isAutomatic))
            ->when($sourceFileId !== null, fn (Builder $query) => $query->where('source_file_id', $sourceFileId))
            ->when($batchId !== null, fn (Builder $query) => $query->where('batch_id', $batchId))
            ->latest('created_at')
            ->paginate($perPage);
    }
}
