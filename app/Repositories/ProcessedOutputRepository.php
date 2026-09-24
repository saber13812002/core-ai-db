<?php

namespace App\Repositories;

use App\Models\ProcessedOutput;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ProcessedOutputRepository extends BaseRepository
{
    public function __construct(ProcessedOutput $model)
    {
        parent::__construct($model);
    }

    /**
     * Paginated list filtered by output type and/or source file —
     * the building block for assembling datasets from metadata filters.
     */
    public function listWithFilters(
        ?int $outputTypeId = null,
        ?string $sourceFileId = null,
        ?string $jobId = null,
        int $perPage = 15,
    ): LengthAwarePaginator {
        return $this->query()
            ->when($outputTypeId !== null, fn (Builder $query) => $query->where('output_type_id', $outputTypeId))
            ->when($sourceFileId !== null, fn (Builder $query) => $query->where('source_file_id', $sourceFileId))
            ->when($jobId !== null, fn (Builder $query) => $query->where('job_id', $jobId))
            ->latest('created_at')
            ->paginate($perPage);
    }
}
