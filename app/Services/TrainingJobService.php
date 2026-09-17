<?php

namespace App\Services;

use App\Repositories\TrainingJobRepository;

class TrainingJobService extends BaseService
{
    public function __construct(TrainingJobRepository $repository)
    {
        parent::__construct($repository);
    }
}
