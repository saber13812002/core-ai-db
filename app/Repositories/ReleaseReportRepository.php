<?php

namespace App\Repositories;

use App\Models\ReleaseReport;

class ReleaseReportRepository extends BaseRepository
{
    public function __construct(ReleaseReport $model)
    {
        parent::__construct($model);
    }

    protected function orderColumn(): ?string
    {
        // No updated_at; generated_at is the only timestamp.
        return 'generated_at';
    }
}
