<?php

namespace App\Services;

use App\Repositories\DatasetItemRepository;

class DatasetItemService extends BaseService
{
    public function __construct(DatasetItemRepository $repository)
    {
        parent::__construct($repository);
    }
}
