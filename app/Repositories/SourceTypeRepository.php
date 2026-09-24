<?php

namespace App\Repositories;

use App\Models\SourceType;

class SourceTypeRepository extends BaseRepository
{
    public function __construct(SourceType $model)
    {
        parent::__construct($model);
    }
}
