<?php

namespace App\Repositories;

use App\Models\AutomationAction;

class AutomationActionRepository extends BaseRepository
{
    public function __construct(AutomationAction $model)
    {
        parent::__construct($model);
    }
}
