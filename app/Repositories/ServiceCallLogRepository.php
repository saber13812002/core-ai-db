<?php

namespace App\Repositories;

use App\Models\ServiceCallLog;

class ServiceCallLogRepository extends BaseRepository
{
    public function __construct(ServiceCallLog $model)
    {
        parent::__construct($model);
    }
}
