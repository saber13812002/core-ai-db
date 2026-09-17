<?php

namespace App\Repositories;

use App\Models\BenchmarkResult;

class BenchmarkResultRepository extends BaseRepository
{
    public function __construct(BenchmarkResult $model)
    {
        parent::__construct($model);
    }
}
