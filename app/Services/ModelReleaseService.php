<?php

namespace App\Services;

use App\Repositories\ModelReleaseRepository;

class ModelReleaseService extends BaseService
{
    public function __construct(ModelReleaseRepository $repository)
    {
        parent::__construct($repository);
    }
}
