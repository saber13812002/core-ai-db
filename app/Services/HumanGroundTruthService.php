<?php

namespace App\Services;

use App\Repositories\HumanGroundTruthRepository;

class HumanGroundTruthService extends BaseService
{
    public function __construct(HumanGroundTruthRepository $repository)
    {
        parent::__construct($repository);
    }
}
