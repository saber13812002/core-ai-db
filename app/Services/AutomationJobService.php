<?php

namespace App\Services;

use App\Repositories\AutomationJobRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AutomationJobService extends BaseService
{
    public function __construct(AutomationJobRepository $repository)
    {
        parent::__construct($repository);
    }

    public function listWithFilters(
        ?string $status = null,
        ?string $source = null,
        ?bool $isAutomatic = null,
        ?string $sourceFileId = null,
        ?string $batchId = null,
        int $perPage = 15,
    ): LengthAwarePaginator {
        return $this->repository->listWithFilters($status, $source, $isAutomatic, $sourceFileId, $batchId, $perPage);
    }
}
