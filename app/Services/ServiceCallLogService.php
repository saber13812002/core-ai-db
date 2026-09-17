<?php

namespace App\Services;

use App\Repositories\ServiceCallLogRepository;

class ServiceCallLogService extends BaseService
{
    public function __construct(ServiceCallLogRepository $repository)
    {
        parent::__construct($repository);
    }
}
