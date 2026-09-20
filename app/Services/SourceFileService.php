<?php

namespace App\Services;

use App\Repositories\SourceFileRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SourceFileService extends BaseService
{
    public function __construct(SourceFileRepository $repository)
    {
        parent::__construct($repository);
    }

    /**
     * @param  array<string, mixed>|null  $metadataFilters
     */
    public function listWithMetadataFilters(?array $metadataFilters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->listWithMetadataFilters($metadataFilters, $perPage);
    }
}
