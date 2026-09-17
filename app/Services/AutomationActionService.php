<?php

namespace App\Services;

use App\Repositories\AutomationActionRepository;

class AutomationActionService extends BaseService
{
    public function __construct(AutomationActionRepository $repository)
    {
        parent::__construct($repository);
    }
}
