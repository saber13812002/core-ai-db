<?php

namespace App\Services;

use App\Repositories\TrainedModelRepository;

class TrainedModelService extends BaseService
{
    public function __construct(TrainedModelRepository $repository)
    {
        parent::__construct($repository);
    }
}
