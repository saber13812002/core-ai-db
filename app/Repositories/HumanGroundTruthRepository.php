<?php

namespace App\Repositories;

use App\Models\HumanGroundTruth;

class HumanGroundTruthRepository extends BaseRepository
{
    public function __construct(HumanGroundTruth $model)
    {
        parent::__construct($model);
    }
}
