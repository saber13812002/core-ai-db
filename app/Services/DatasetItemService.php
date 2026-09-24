<?php

namespace App\Services;

use App\Repositories\DatasetItemRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DatasetItemService extends BaseService
{
    public function __construct(DatasetItemRepository $repository)
    {
        parent::__construct($repository);
    }

    public function listWithFilters(
        ?string $datasetId = null,
        ?string $split = null,
        int $perPage = 15,
    ): LengthAwarePaginator {
        return $this->repository->listWithFilters($datasetId, $split, $perPage);
    }
}
