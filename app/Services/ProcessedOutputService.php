<?php

namespace App\Services;

use App\Repositories\ProcessedOutputRepository;

class ProcessedOutputService extends BaseService
{
    public function __construct(ProcessedOutputRepository $repository)
    {
        parent::__construct($repository);
    }
}
