<?php

namespace App\Repositories;

use App\Models\AutomationJob;

class AutomationJobRepository extends BaseRepository
{
    public function __construct(AutomationJob $model)
    {
        parent::__construct($model);
    }
}
