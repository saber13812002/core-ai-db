<?php

namespace App\Services;

use App\Repositories\BenchmarkResultRepository;

class BenchmarkResultService extends BaseService
{
    public function __construct(BenchmarkResultRepository $repository)
    {
        parent::__construct($repository);
    }
}
