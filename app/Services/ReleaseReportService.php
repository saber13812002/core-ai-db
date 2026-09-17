<?php

namespace App\Services;

use App\Repositories\ReleaseReportRepository;

class ReleaseReportService extends BaseService
{
    public function __construct(ReleaseReportRepository $repository)
    {
        parent::__construct($repository);
    }
}
