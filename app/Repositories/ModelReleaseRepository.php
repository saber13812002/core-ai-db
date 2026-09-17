<?php

namespace App\Repositories;

use App\Models\ModelRelease;

class ModelReleaseRepository extends BaseRepository
{
    public function __construct(ModelRelease $model)
    {
        parent::__construct($model);
    }
}
