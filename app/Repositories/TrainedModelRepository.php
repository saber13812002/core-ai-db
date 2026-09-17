<?php

namespace App\Repositories;

use App\Models\TrainedModel;

class TrainedModelRepository extends BaseRepository
{
    public function __construct(TrainedModel $model)
    {
        parent::__construct($model);
    }
}
