<?php

namespace App\Services;

use App\Repositories\ProcessedOutputRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProcessedOutputService extends BaseService
{
    public function __construct(ProcessedOutputRepository $repository)
    {
        parent::__construct($repository);
    }

    public function listWithFilters(
        ?int $outputTypeId = null,
        ?string $sourceFileId = null,
        ?string $jobId = null,
        int $perPage = 15,
    ): LengthAwarePaginator {
        return $this->repository->listWithFilters($outputTypeId, $sourceFileId, $jobId, $perPage);
    }
}
