<?php

namespace App\Repositories;

use App\Models\Dataset;

class DatasetRepository extends BaseRepository
{
    public function __construct(Dataset $model)
    {
        parent::__construct($model);
    }
}
