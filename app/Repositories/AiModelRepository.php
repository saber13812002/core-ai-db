<?php

namespace App\Repositories;

use App\Models\AiModel;

class AiModelRepository extends BaseRepository
{
    public function __construct(AiModel $model)
    {
        parent::__construct($model);
    }
}
