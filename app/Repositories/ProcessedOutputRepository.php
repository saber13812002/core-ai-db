<?php

namespace App\Repositories;

use App\Models\ProcessedOutput;

class ProcessedOutputRepository extends BaseRepository
{
    public function __construct(ProcessedOutput $model)
    {
        parent::__construct($model);
    }
}
