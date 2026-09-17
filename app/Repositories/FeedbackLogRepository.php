<?php

namespace App\Repositories;

use App\Models\FeedbackLog;

class FeedbackLogRepository extends BaseRepository
{
    public function __construct(FeedbackLog $model)
    {
        parent::__construct($model);
    }
}
