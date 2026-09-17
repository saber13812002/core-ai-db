<?php

namespace App\Repositories;

use App\Models\TrainingJob;

class TrainingJobRepository extends BaseRepository
{
    public function __construct(TrainingJob $model)
    {
        parent::__construct($model);
    }
}
