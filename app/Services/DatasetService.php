<?php

namespace App\Services;

use App\Repositories\DatasetRepository;

class DatasetService extends BaseService
{
    public function __construct(DatasetRepository $repository)
    {
        parent::__construct($repository);
    }
}
