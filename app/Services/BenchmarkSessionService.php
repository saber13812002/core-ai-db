<?php

namespace App\Services;

use App\Repositories\BenchmarkSessionRepository;

class BenchmarkSessionService extends BaseService
{
    public function __construct(BenchmarkSessionRepository $repository)
    {
        parent::__construct($repository);
    }
}
