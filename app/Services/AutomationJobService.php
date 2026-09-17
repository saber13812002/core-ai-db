<?php

namespace App\Services;

use App\Repositories\AutomationJobRepository;

class AutomationJobService extends BaseService
{
    public function __construct(AutomationJobRepository $repository)
    {
        parent::__construct($repository);
    }
}
