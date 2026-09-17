<?php

namespace App\Services;

use App\Repositories\AutomationFlowRepository;

class AutomationFlowService extends BaseService
{
    public function __construct(AutomationFlowRepository $repository)
    {
        parent::__construct($repository);
    }
}
