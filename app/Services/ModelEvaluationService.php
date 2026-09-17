<?php

namespace App\Services;

use App\Repositories\ModelEvaluationRepository;

class ModelEvaluationService extends BaseService
{
    public function __construct(ModelEvaluationRepository $repository)
    {
        parent::__construct($repository);
    }
}
