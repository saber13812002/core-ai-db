<?php

namespace App\Repositories;

use App\Models\CleanedOutput;

class CleanedOutputRepository extends BaseRepository
{
    public function __construct(CleanedOutput $model)
    {
        parent::__construct($model);
    }
}
