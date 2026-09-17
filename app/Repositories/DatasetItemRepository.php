<?php

namespace App\Repositories;

use App\Models\DatasetItem;

class DatasetItemRepository extends BaseRepository
{
    public function __construct(DatasetItem $model)
    {
        parent::__construct($model);
    }

    protected function orderColumn(): ?string
    {
        // created_at-only table: UUID ids have no insertion order.
        return null;
    }
}
