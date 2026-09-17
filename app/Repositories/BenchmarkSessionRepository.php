<?php

namespace App\Repositories;

use App\Models\BenchmarkSession;

class BenchmarkSessionRepository extends BaseRepository
{
    public function __construct(BenchmarkSession $model)
    {
        parent::__construct($model);
    }
}
