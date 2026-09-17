<?php

namespace App\Repositories;

use App\Models\OutputType;

class OutputTypeRepository extends BaseRepository
{
    public function __construct(OutputType $model)
    {
        parent::__construct($model);
    }
}
