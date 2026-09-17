<?php

namespace App\Services;

use App\Repositories\FeedbackLogRepository;

class FeedbackLogService extends BaseService
{
    public function __construct(FeedbackLogRepository $repository)
    {
        parent::__construct($repository);
    }
}
