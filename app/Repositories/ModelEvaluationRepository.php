<?php

namespace App\Repositories;

use App\Models\ModelEvaluation;

class ModelEvaluationRepository extends BaseRepository
{
    public function __construct(ModelEvaluation $model)
    {
        parent::__construct($model);
    }
}
