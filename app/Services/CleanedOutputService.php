<?php

namespace App\Services;

use App\Repositories\CleanedOutputRepository;

class CleanedOutputService extends BaseService
{
    public function __construct(CleanedOutputRepository $repository)
    {
        parent::__construct($repository);
    }
}
