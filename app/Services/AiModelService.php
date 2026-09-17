<?php

namespace App\Services;

use App\Repositories\AiModelRepository;

class AiModelService extends BaseService
{
    public function __construct(AiModelRepository $repository)
    {
        parent::__construct($repository);
    }
}
