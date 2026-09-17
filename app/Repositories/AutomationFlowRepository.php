<?php

namespace App\Repositories;

use App\Models\AutomationFlow;

class AutomationFlowRepository extends BaseRepository
{
    public function __construct(AutomationFlow $model)
    {
        parent::__construct($model);
    }
}
